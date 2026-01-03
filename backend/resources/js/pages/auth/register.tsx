import RegisteredUserController from '@/actions/App/Http/Controllers/Auth/RegisteredUserController';
import { login } from '@/routes';
import { Form, Head } from '@inertiajs/react';
import { LoaderCircle } from 'lucide-react';

import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/auth-layout';

export default function Register() {
    return (
        <div
            style={{
                backgroundImage: "url('/images/welcome/redwhitebg.jpg')",
                backgroundRepeat: 'no-repeat',
                backgroundPosition: 'bottom center',
                backgroundSize: 'cover',
            }}
            className="flex min-h-screen items-center justify-center p-6 text-[var(--foreground)]"
        >
            <AuthLayout
                title="Create an account"
                description="Masukkan detail Anda di bawah ini untuk membuat akun Anda."
            >
                <Head title="Register" />
                <Form
                {...RegisteredUserController.store.form()}
                resetOnSuccess={['password', 'password_confirmation']}
                disableWhileProcessing
                className="flex flex-col gap-6"
                >
                    {({ processing, errors }) => (
                        <>
                        <div className="grid gap-6">
                            <div className="grid gap-2">
                                <Label htmlFor="nama">Nama</Label>
                                <Input
                                    id="nama"
                                    type="text"
                                    required
                                    autoFocus
                                    tabIndex={1}
                                    autoComplete="name"
                                    name="nama"
                                    placeholder="Masukkan Nama"
                                />
                                <InputError message={errors.nama} className="mt-2"/>
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="email">Alamat Email</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    required
                                    tabIndex={2}
                                    autoComplete="email"
                                    name="email"
                                    placeholder="email@example.com"
                                />
                                <InputError message={errors.email} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="nim">NIM</Label>
                                <Input
                                    id="nim"
                                    type="text"
                                    required
                                    tabIndex={3}
                                    name="nim"
                                    placeholder="Masukkan NIM"
                                />
                                <InputError message={errors.nim} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="kelas">Kelas</Label>
                                <Input
                                    id="kelas"
                                    type="text"
                                    required
                                    tabIndex={4}
                                    name="kelas"
                                    placeholder="Masukkan Kelas"
                                />
                                <InputError message={errors.kelas} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="prodi">Program Studi</Label>
                                <Input
                                    id="prodi"
                                    type="text"
                                    required
                                    tabIndex={5}
                                    name="prodi"
                                    placeholder="Masukkan Program Studi"
                                />
                                <InputError message={errors.prodi} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="password">Password</Label>
                                <Input
                                    id="password"
                                    type="password"
                                    required
                                    tabIndex={6}
                                    autoComplete="new-password"
                                    name="password"
                                    placeholder="Masukkan Password"
                                    />
                                <InputError message={errors.password} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="password_confirmation">Konfirmasi Password</Label>
                                <Input
                                    id="password_confirmation"
                                    type="password"
                                    required
                                    tabIndex={7}
                                    autoComplete="new-password"
                                    name="password_confirmation"
                                    placeholder="Masukkan Password"/>
                                <InputError message={errors.password_confirmation} />
                            </div>

                            <Button
                                type="submit"
                                className="mt-4 w-full text-[var(--primary-foreground)] hover-redbg-1 hover:hover-redbg-2"
                                tabIndex={8}
                                data-test="register-user-button"
                                >
                                {processing && (<LoaderCircle className="h-4 w-4 animate-spin" />)}
                                Buat akun
                            </Button>
                        </div>

                        <div className="text-center text-sm text-muted-foreground">
                            Already have an account?{' '}
                            <TextLink href={login()} tabIndex={9}>
                                Log in
                            </TextLink>
                        </div>
                        </>
                )}
                </Form>
                </AuthLayout>
        </div>
    );
}