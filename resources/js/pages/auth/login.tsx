import AuthenticatedSessionController from '@/actions/App/Http/Controllers/Auth/AuthenticatedSessionController';
import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/auth-layout';
import { register } from '@/routes';
import { request } from '@/routes/password';
import { Form, Head } from '@inertiajs/react';
import { LoaderCircle } from 'lucide-react';
import { useState } from 'react';

interface LoginProps {
    status?: string;
    canResetPassword: boolean;
}

export default function Login({ status, canResetPassword }: LoginProps) {
    const [role, setRole] = useState<'buyer' | 'seller'>('buyer');

    return (
        <AuthLayout
            title="Masuk — Aplikasi Koperasi TEL-U"
            description="Masuk menggunakan email kampus dan kata sandi kamu untuk mengakses layanan koperasi Telkom University"
        >
            <Head title="Masuk — Koperasi TEL-U" />

            <Form
                {...AuthenticatedSessionController.store.form()}
               
                data={{ role }} 
                resetOnSuccess={['password']}
                className="flex flex-col gap-6"
            >
                {({ processing, errors }) => (
                    <>
                        <div className="grid gap-6">
                            <div className="grid gap-2">
                                <Label>Masuk sebagai</Label>
                                <div className="flex space-x-3">
                                    <label className="inline-flex items-center space-x-2">
                                        <input
                                            type="radio"
                                            name="role"
                                            value="buyer"
                                            checked={role === 'buyer'}
                                            onChange={() => setRole('buyer')}
                                            tabIndex={1}
                                        />
                                        <span>Pembeli</span>
                                    </label>
                                    <label className="inline-flex items-center space-x-2">
                                        <input
                                            type="radio"
                                            name="role"
                                            value="seller"
                                            checked={role === 'seller'}
                                            onChange={() => setRole('seller')}
                                            tabIndex={2}
                                        />
                                        <span>Penjual</span>
                                    </label>
                                </div>
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="email">Email kampus</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    name="email"
                                    required
                                    autoFocus
                                    tabIndex={3}
                                    autoComplete="email"
                                    placeholder="email@telkomuniversity.ac.id"
                                />
                                <InputError message={errors.email} />
                            </div>

                            <div className="grid gap-2">
                                <div className="flex items-center">
                                    <Label htmlFor="password">Kata sandi</Label>
                                    {canResetPassword && (
                                        <TextLink
                                            href={request()}
                                            className="ml-auto text-sm"
                                            tabIndex={6}
                                        >
                                            Lupa kata sandi?
                                        </TextLink>
                                    )}
                                </div>
                                <Input
                                    id="password"
                                    type="password"
                                    name="password"
                                    required
                                    tabIndex={4}
                                    autoComplete="current-password"
                                    placeholder="Masukkan kata sandi"
                                />
                                <InputError message={errors.password} />
                            </div>

                            <div className="flex items-center space-x-3">
                                <Checkbox id="remember" name="remember" tabIndex={5} />
                                <Label htmlFor="remember">Ingat saya</Label>
                            </div>

                            {/* Hidden input agar Inertia/Laravel menerima role saat submit */}
                            <input type="hidden" name="role" value={role} />

                            <Button
                                type="submit"
                                className="mt-4 w-full"
                                tabIndex={7}
                                disabled={processing}
                                data-test="login-button"
                            >
                                {processing && (
                                    <LoaderCircle className="h-4 w-4 animate-spin mr-2 inline-block" />
                                )}
                                Masuk
                            </Button>
                        </div>

                        <div className="text-center text-sm text-muted-foreground">
                            Belum punya akun koperasi?{' '}
                            <TextLink href={register()} tabIndex={8}>
                                Daftar sekarang
                            </TextLink>
                        </div>
                    </>
                )}
            </Form>

            {status && (
                <div className="mb-4 text-center text-sm font-medium text-emerald-600">
                    {status}
                </div>
            )}
        </AuthLayout>
    );
}
