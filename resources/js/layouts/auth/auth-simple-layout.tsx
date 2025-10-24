// resources/js/layouts/auth/auth-simple-layout.tsx
import AppLogoIcon from '@/components/app-logo-icon';
import { Link } from '@inertiajs/react';
import { route } from 'ziggy-js';
import { type PropsWithChildren } from 'react';

interface AuthLayoutProps {
    name?: string;
    title?: string;
    description?: string;
}

export default function AuthSimpleLayout({
    children,
    title,
    description,
}: PropsWithChildren<AuthLayoutProps>) {
    const hasDashboard =
        typeof window !== 'undefined' &&
        (window as any).Ziggy?.routes?.dashboard;

    const homeUrl = hasDashboard ? route('dashboard') : '/';

    return (
        <div className="flex min-h-svh flex-col items-center justify-center gap-6 bg-background p-6 md:p-10">
            <div className="w-full max-w-sm">
                <div className="flex flex-col gap-8">
                    <div className="flex flex-col items-center gap-4">
                        <Link href={homeUrl} className="flex flex-col items-center gap-2 font-medium">
                            <div className="mb-1 flex h-9 w-9 items-center justify-center rounded-md">
                                <AppLogoIcon className="size-9 fill-current text-[var(--foreground)] dark:text-white" />
                            </div>
                            {/* pakai sr-only untuk aksesibilitas; aman meski title undefined */}
                            <span className="sr-only">{title ?? 'Home'}</span>
                        </Link>

                        <div className="space-y-2 text-center">
                            {title && <h1 className="text-xl font-medium">{title}</h1>}
                            {description && (
                                <p className="text-center text-sm text-muted-foreground">
                                    {description}
                                </p>
                            )}
                        </div>
                    </div>

                    {children}
                </div>
            </div>
        </div>
    );
}
