import { Head, Form } from '@inertiajs/react';
import route from 'ziggy-js';
import { LoaderCircle } from 'lucide-react';

import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/auth-layout';

export default function Register() {
    return (
        <AuthLayout
            title="Create your account"
            description="Join Tel-U Shop and start managing orders in minutes."
            variant="split"
        >
            <Head title="Register" />

            <Form
                action={route('register.store')}
                method="post"
                className="space-y-6"
            >
                {({ processing, errors }) => (
                    <>
                        <div className="grid gap-5">
                            <div className="grid gap-2">
                                <Label htmlFor="name">Name</Label>
                                <Input
                                    id="name"
                                    name="name"
                                    required
                                    placeholder="Full name"
                                    className="h-12 rounded-xl bg-muted/70 border-border/60 focus-visible:border-primary/50 focus-visible:ring-primary/20"
                                />
                                <InputError
                                    message={errors.name}
                                    className="mt-2"
                                />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="username">Username</Label>
                                <Input
                                    id="username"
                                    name="username"
                                    required
                                    placeholder="Choose a username"
                                    className="h-12 rounded-xl bg-muted/70 border-border/60 focus-visible:border-primary/50 focus-visible:ring-primary/20"
                                />
                                <InputError message={errors.username} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="email">Email address</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    name="email"
                                    required
                                    placeholder="email@example.com"
                                    className="h-12 rounded-xl bg-muted/70 border-border/60 focus-visible:border-primary/50 focus-visible:ring-primary/20"
                                />
                                <InputError message={errors.email} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="password">Password</Label>
                                <Input
                                    id="password"
                                    type="password"
                                    name="password"
                                    required
                                    placeholder="Create a password"
                                    className="h-12 rounded-xl bg-muted/70 border-border/60 focus-visible:border-primary/50 focus-visible:ring-primary/20"
                                />
                                <InputError message={errors.password} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="password_confirmation">
                                    Confirm password
                                </Label>
                                <Input
                                    id="password_confirmation"
                                    type="password"
                                    name="password_confirmation"
                                    required
                                    placeholder="Confirm password"
                                    className="h-12 rounded-xl bg-muted/70 border-border/60 focus-visible:border-primary/50 focus-visible:ring-primary/20"
                                />
                                <InputError
                                    message={errors.password_confirmation}
                                />
                            </div>

                            <Button
                                type="submit"
                                className="mt-2 h-12 w-full text-base font-semibold"
                            >
                                {processing && (
                                    <LoaderCircle className="h-4 w-4 animate-spin" />
                                )}
                                Create account
                            </Button>
                        </div>

                        <div className="text-center text-sm text-muted-foreground">
                            Already have an account?{' '}
                            <TextLink href={route('login')}>Log in</TextLink>
                        </div>
                    </>
                )}
            </Form>
        </AuthLayout>
    );
}
