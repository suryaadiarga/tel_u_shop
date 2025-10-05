import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

export default function Dashboard() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="relative w-full flex flex-col items-center">
                {/* Notifikasi */}
                <button className="absolute top-0 right-0 mt-2 mr-4 rounded-full border-2 border-[#bf1206] p-2">
                    <svg
                        className="text-[#bf1206]"
                        width="24"
                        height="24"
                        fill="none"
                        stroke="currentColor"
                        strokeWidth="2"
                        viewBox="0 0 24 24"
                    >
                        <path d="M18 8a6 6 0 10-12 0c0 7-3 9-3 9h18s-3-2-3-9" />
                        <path d="M13.73 21a2 2 0 01-3.46 0" />
                    </svg>
                </button>
                <div className="flex flex-col items-center w-full max-w-md gap-3 mt-8">
                    {/* Search Bar */}
                    <div className="relative w-full">
                        <input
                            type="text"
                            placeholder="Cari Item"
                            className="w-full rounded-lg bg-[#bf1206] bg-opacity-90 text-white placeholder-white px-4 py-2 focus:outline-none"
                        />
                        <svg
                            className="absolute left-3 top-1/2 -translate-y-1/2 text-white opacity-80"
                            width="18"
                            height="18"
                            fill="none"
                            stroke="currentColor"
                            strokeWidth="2"
                            viewBox="0 0 24 24"
                        >
                            <circle cx="11" cy="11" r="8" />
                            <line x1="21" y1="21" x2="16.65" y2="16.65" />
                        </svg>
                    </div>
                    {/* Saldo */}
                    <div className="flex items-center bg-white rounded-lg shadow px-4 py-2 w-full justify-center">
                        <svg
                            className="text-[#bf1206] mr-2"
                            width="20"
                            height="20"
                            fill="none"
                            stroke="currentColor"
                            strokeWidth="2"
                            viewBox="0 0 24 24"
                        >
                            <rect x="2" y="7" width="20" height="14" rx="4" />
                            <path d="M16 3v4M8 3v4" />
                        </svg>
                        <span className="font-medium text-[#bf1206]">Rp 45.000</span>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
