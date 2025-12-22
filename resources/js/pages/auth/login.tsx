import { Head, Form } from '@inertiajs/react';
import { route } from 'ziggy-js';
import { LoaderCircle } from 'lucide-react';

import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/auth-layout';

export default function Login() {
    return (
        <AuthLayout
            title="Welcome back"
            description="Sign in to continue managing Tel-U Shop."
            variant="split"
        >
            <Head title="Login" />

            <Form
                action={route('login.post')}
                method="post"
                className="space-y-6"
            >
                {({ processing, errors }) => (
                    <>
                        <div className="grid gap-5">
                            <div className="grid gap-2">
                                <Label htmlFor="username">Username</Label>
                                <Input
                                    id="username"
                                    name="username"
                                    required
                                    autoFocus
                                    placeholder="Enter your username"
                                    className="h-12 rounded-xl bg-muted/70 border-border/60 focus-visible:border-primary/50 focus-visible:ring-primary/20"
                                />
                                <InputError
                                    message={errors.username}
                                    className="mt-2"
                                />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="password">Password</Label>
                                <Input
                                    id="password"
                                    name="password"
                                    type="password"
                                    required
                                    placeholder="Enter your password"
                                    className="h-12 rounded-xl bg-muted/70 border-border/60 focus-visible:border-primary/50 focus-visible:ring-primary/20"
                                />
                                <InputError message={errors.password} />
                            </div>

                            <label className="flex items-center gap-2 text-sm text-muted-foreground">
                                <input
                                    type="checkbox"
                                    name="remember"
                                    className="size-4 rounded border-border/60 text-primary accent-primary"
                                />
                                Remember me
                            </label>

                            <Button
                                type="submit"
                                className="mt-2 h-12 w-full text-base font-semibold"
                            >
                                {processing && (
                                    <LoaderCircle className="h-4 w-4 animate-spin" />
                                )}
                                Log in
                            </Button>
                        </div>

                        <div className="text-center text-sm text-muted-foreground">
                            Don't have an account?{' '}
                            <TextLink href={route('register')}>Sign up</TextLink>
                        </div>
                    </>
                )}
            </Form>
        </AuthLayout>
    );
}
