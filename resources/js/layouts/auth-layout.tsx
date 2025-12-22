import AppLogoIcon from '@/components/app-logo-icon';
import { Link } from '@inertiajs/react';

type AuthLayoutProps = {
    children: React.ReactNode;
    title: string;
    description?: string;
    variant?: 'centered' | 'split';
};

export default function AuthLayout({
    children,
    title,
    description,
    variant = 'centered',
}: AuthLayoutProps) {
    if (variant === 'centered') {
        return (
            <div className="flex min-h-svh flex-col items-center justify-center bg-gradient-to-br from-slate-50 via-white to-red-50 px-4 py-10">
                <div className="w-full max-w-md rounded-3xl border border-border/70 bg-card p-8 shadow-lg transition-shadow duration-200 hover:shadow-xl">
                    <div className="mb-8 flex flex-col items-center gap-4 text-center">
                        <Link
                            href="/"
                            className="flex flex-col items-center gap-2 font-medium"
                        >
                            <div className="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary text-primary-foreground shadow-md">
                                <AppLogoIcon className="size-7 fill-current text-white" />
                            </div>
                            <span className="sr-only">{title}</span>
                        </Link>
                        <div className="space-y-2">
                            <h1 className="text-2xl font-semibold">{title}</h1>
                            {description && (
                                <p className="text-sm text-muted-foreground">
                                    {description}
                                </p>
                            )}
                        </div>
                    </div>
                    {children}
                </div>
            </div>
        );
    }

    return (
        <div className="min-h-screen bg-gradient-to-br from-slate-50 via-white to-red-50">
            <div className="mx-auto flex min-h-screen w-full max-w-6xl items-center px-4 py-10 sm:px-6 lg:px-8">
                <div className="grid w-full overflow-hidden rounded-3xl border border-border/70 bg-card shadow-xl lg:grid-cols-[1.1fr_1fr]">
                    <div className="relative hidden flex-col justify-between bg-[radial-gradient(circle_at_top,_rgba(214,31,31,0.35),_transparent_55%)] p-10 text-slate-900 lg:flex">
                        <div className="flex items-center gap-3">
                            <div className="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary text-primary-foreground shadow-md">
                                <AppLogoIcon className="size-7 fill-current text-white" />
                            </div>
                            <div>
                                <p className="text-sm uppercase tracking-[0.2em] text-slate-500">
                                    Tel-U Shop
                                </p>
                                <p className="text-xl font-semibold">
                                    Admin Console
                                </p>
                            </div>
                        </div>

                        <div className="space-y-4">
                            <h2 className="text-3xl font-semibold leading-tight">
                                Everything you need to manage your store.
                            </h2>
                            <p className="max-w-md text-sm text-slate-600">
                                Monitor orders, manage products, and keep your
                                team aligned with a premium, modern dashboard.
                            </p>
                            <div className="inline-flex items-center gap-2 rounded-full bg-white/80 px-4 py-2 text-xs font-semibold uppercase tracking-[0.24em] text-slate-600 shadow-sm">
                                Secure access
                            </div>
                        </div>
                    </div>

                    <div className="flex flex-col items-center justify-center p-6 sm:p-10">
                        <div className="w-full max-w-md rounded-3xl border border-border/70 bg-white/85 p-8 shadow-lg backdrop-blur transition-shadow duration-200 hover:shadow-xl">
                            <div className="mb-6 space-y-2 text-center">
                                <p className="text-xs font-semibold uppercase tracking-[0.25em] text-slate-400">
                                    Authentication
                                </p>
                                <h1 className="text-2xl font-semibold">
                                    {title}
                                </h1>
                                {description && (
                                    <p className="text-sm text-muted-foreground">
                                        {description}
                                    </p>
                                )}
                            </div>
                            {children}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
