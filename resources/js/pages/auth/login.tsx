import { Head, Form } from '@inertiajs/react';
import { route } from 'ziggy-js';
import { LoaderCircle, Route } from 'lucide-react';

import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/auth-layout';

export default function Login() {
    return (
        <AuthLayout
            title="Log in to your account"
            description="Enter your email and password below to log in"
        >
            <Head title="Login" />

            {/* ➜ gunakan URL dari Ziggy */}
            <Form action={route ('login.post')} method="post" className="flex flex-col gap-6">
                {({ processing, errors }) => (
                    <>
                        <div className="grid gap-6">
                            <div className="grid gap-2">
                                <Label htmlFor="username">Username</Label>
                                <Input id="username" name="username" required autoFocus placeholder="username" />
                                <InputError message={errors.username} className="mt-2" />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="password">Password</Label>
                                <Input id="password" name="password" type="password" required placeholder="Password" />
                                <InputError message={errors.password} />
                            </div>

                            <label className="flex items-center gap-2 text-sm">
                                <input type="checkbox" name="remember" className="size-4" /> Remember me
                            </label>

                            <Button type="submit" className="mt-2 w-full">
                                {processing && <LoaderCircle className="h-4 w-4 animate-spin" />}
                                Log in
                            </Button>
                        </div>

                        <div className="text-center text-sm text-muted-foreground">
                            Don’t have an account?{' '}
                            <TextLink href={route('register')}>Sign up</TextLink>
                        </div>
                    </>
                )}
            </Form>
        </AuthLayout>
    );
}
