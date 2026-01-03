import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';


const breadcrumbs: BreadcrumbItem[] = [
    { title: 'History', href: '/' },
]

// Dummy data pembelian
const transactionDetail = [
    {
        id: 1,
        name: 'Ayam Geprek',
        image: '/images/makanan/AyamGeprek.jpg',
        category: 'Makanan',
        date: '09.00 10-12-2025',
        methodpay: 'Dana',
        price: 'Rp 12.000',
        status: 'Paid'
    },
    {
        id: 2,
        name:
        'Es Kopi Susu',
        image: '/images/minuman/KopiSusu.jpg',
        category: 'Minuman',
        date: '09.30 10-12-2025',
        methodpay: 'QRis',
        price: 'Rp 9.000',
        status: 'Pending'
    },
    {
        id: 3,
        name: 'Paket Data 10Gb',
        image: '/images/lainlain/paketdata10gb.jpg',
        category: 'Lainnya',
        date: '10.00 10-12-2025',
        methodpay: 'Gopay',
        price: 'Rp 25.000',
        status: 'Cancelled'
    },
];

const statusColor = {
    Pending: 'bg-gray-200 text-gray-800',
    Paid: 'bg-green-200 text-green-800',
    Cancelled: 'bg-red-200 text-red-800',
  };

export default function History() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="History"/>
                  <div className="p-6 space-y-6">

                    {transactionDetail.map((item) => (
                    <div
                        key={item.id}
                        className="flex flex-col sm:flex-row justify-between border p-4 rounded-lg shadow-sm gap-4"
                        >
                        <div className="flex items-center gap-4">
                        <img
                            src={item.image}
                            alt={item.name}
                            className="w-20 h-20 object-cover rounded-md"
                        />
                        <div className="space-y-1">
                            <h2 className="text-lg font-semibold">{item.name}</h2>
                            <div className="text-sm text-muted-foreground">
                            <span className="font-medium">Kategori :</span> {item.category}
                            </div>
                            <div className="text-sm text-muted-foreground">
                            <span className="font-medium">Tanggal & Waktu :</span> {item.date}
                            </div>
                            <div className="text-sm text-muted-foreground">
                            <span className="font-medium">Metode Pembayaran :</span> {item.methodpay}
                            </div>
                            </div>
                        </div>

                        <div className="text-right sm:text-left sm:mt-0 mt-4">
                        <div className="text-xl font-bold text-[var(--destructive)]">{item.price}</div>
                        <div className="text-sm text-muted-foreground">ID Pesanan : #{item.id}</div>
                        <div>
                            <div className={`inline-block px-2 py-1 text-sm rounded ${statusColor[item.status]}`}>{item.status}</div>
                        </div>
                        </div>
                    </div>
                    ))}
                </div>
        </AppLayout>
    );
}