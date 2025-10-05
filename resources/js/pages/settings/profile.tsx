import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import { send } from '@/routes/verification';
import { type BreadcrumbItem, type SharedData } from '@/types';
import { Transition } from '@headlessui/react';
import { Form, Head, Link, usePage } from '@inertiajs/react';

import DeleteUser from '@/components/delete-user';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import SettingsLayout from '@/layouts/settings/layout';
import { edit } from '@/routes/profile';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Pengaturan',
        href: edit().url,
    },
];

export default function Profile({ mustVerifyEmail, status }: { mustVerifyEmail: boolean; status?: string }) {
    const { auth } = usePage<SharedData>().props;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Pengaturan" />
            <SettingsLayout>
                <div className="max-w-xl mx-auto bg-white rounded-2xl shadow-lg p-8 flex flex-col items-center gap-8">
                    {/* Avatar Section */}
                    <div className="flex flex-col items-center gap-2">
                        <div className="w-24 h-24 rounded-full bg-gray-200 overflow-hidden flex items-center justify-center">
                            {/* Avatar image, fallback to initials if not available */}
                            {typeof auth.user.avatar_url === 'string' && auth.user.avatar_url.length > 0 ? (
                                <img src={auth.user.avatar_url} alt="Avatar" className="w-full h-full object-cover" />
                            ) : (
                                <span className="text-3xl font-bold text-gray-500">
                                    {auth.user.name?.split(' ').map(n => n[0]).join('')}
                                </span>
                            )}
                        </div>
                        <span className="font-semibold text-lg text-gray-900">{auth.user.name}</span>
                        <span className="text-sm text-gray-500">{auth.user.email}</span>
                    </div>

                    {/* Form Section */}
                    <Form
                        {...ProfileController.update.form()}
                        options={{ preserveScroll: true }}
                        className="w-full space-y-6"
                    >
                        {({ processing, recentlySuccessful, errors }) => (
                            <>
                                <div className="flex flex-col gap-2">
                                    <Label htmlFor="name" className="text-base font-medium text-gray-700">Nama Lengkap</Label>
                                    <Input
                                        id="name"
                                        className="mt-1 block w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary"
                                        defaultValue={auth.user.name}
                                        name="name"
                                        required
                                        autoComplete="name"
                                        placeholder="Nama Lengkap"
                                    />
                                    <InputError className="mt-2" message={errors.name} />
                                </div>
                                <div className="flex flex-col gap-2">
                                    <Label htmlFor="email" className="text-base font-medium text-gray-700">Email</Label>
                                    <Input
                                        id="email"
                                        type="email"
                                        className="mt-1 block w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary"
                                        defaultValue={auth.user.email}
                                        name="email"
                                        required
                                        autoComplete="username"
                                        placeholder="Email"
                                    />
                                    <InputError className="mt-2" message={errors.email} />
                                </div>
                                {mustVerifyEmail && auth.user.email_verified_at === null && (
                                    <div className="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mt-2">
                                        <p className="text-sm text-yellow-700">
                                            Email Anda belum terverifikasi.{' '}
                                            <Link
                                                href={send()}
                                                as="button"
                                                className="text-primary underline underline-offset-4 hover:text-primary-dark"
                                            >
                                                Klik di sini untuk mengirim ulang email verifikasi.
                                            </Link>
                                        </p>
                                        {status === 'verification-link-sent' && (
                                            <div className="mt-2 text-sm font-medium text-green-600">
                                                Link verifikasi baru telah dikirim ke email Anda.
                                            </div>
                                        )}
                                    </div>
                                )}
                                <div className="flex items-center gap-4 mt-4">
                                    <Button disabled={processing} data-test="update-profile-button" className="px-6 py-2 rounded-lg bg-primary text-white hover:bg-primary-dark transition">
                                        Simpan
                                    </Button>
                                    <Transition
                                        show={recentlySuccessful}
                                        enter="transition ease-in-out"
                                        enterFrom="opacity-0"
                                        leave="transition ease-in-out"
                                        leaveTo="opacity-0"
                                    >
                                        <p className="text-sm text-green-600">Tersimpan</p>
                                    </Transition>
                                </div>
                            </>
                        )}
                    </Form>
                </div>
                {/* Delete User Section, styled to match Figma */}
                <div className="max-w-xl mx-auto mt-8">
                    <DeleteUser />
                </div>
            </SettingsLayout>
        </AppLayout>
    );
}
