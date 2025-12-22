import { SidebarInset } from '@/components/ui/sidebar';
import * as React from 'react';

interface AppContentProps extends React.ComponentProps<'main'> {
    variant?: 'header' | 'sidebar';
}

export function AppContent({
    variant = 'header',
    children,
    ...props
}: AppContentProps) {
    if (variant === 'sidebar') {
        return (
            <SidebarInset {...props}>
                <div className="m-4 rounded-3xl border border-border/70 bg-card p-6 shadow-sm md:m-6 md:p-8">
                    {children}
                </div>
            </SidebarInset>
        );
    }

    return (
        <main
            className="mx-auto flex h-full w-full max-w-7xl flex-1 flex-col gap-8 rounded-3xl border border-border/70 bg-card px-6 py-8 shadow-sm md:px-8 lg:px-10"
            {...props}
        >
            {children}
        </main>
    );
}
