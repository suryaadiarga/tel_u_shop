import { Head } from '@inertiajs/react';

// Appearance settings removed. Kept placeholder page to avoid breaking routes.
import AppearanceTabs from '@/components/appearance-tabs';
import HeadingSmall from '@/components/heading-small';
import { type BreadcrumbItem } from '@/types';

import AppLayout from '@/layouts/app-layout';
import SettingsLayout from '@/layouts/settings/layout';
import { edit as editAppearance } from '@/routes/appearance';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Appearance settings',
        href: editAppearance().url,
    },
];

export default function Appearance() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Appearance settings" />

            <SettingsLayout>
                <div className="space-y-6">
                    <HeadingSmall
                        title="Appearance settings"
                        description="Appearance settings have been removed; the application uses a single light theme."
                    />
                    <div className="text-sm text-neutral-600">
                        The appearance controls have been disabled. The app uses the site-wide theme.
                    </div>
                </div>
            </SettingsLayout>
        </AppLayout>
    );
}
