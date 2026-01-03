import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';


const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Shopping Cart', href: '/' },
]

const cartItems = [
    {
        id: 1,
        name: 'Ayam Geprek',
        image: '/images/makanan/AyamGeprek.jpg',
        status: 'Pending',
        price: 'Rp 12.000',
    },
    {
        id: 2,
        name: 'Es Kopi Susu',
        image: '/images/minuman/KopiSusu.jpg',
        status: 'Pending',
        price: 'Rp 9.000',
    }
]

const statusColor = {
    Pending: 'bg-gray-200 text-gray-800',
    Paid: 'bg-green-200 text-green-800',
    Cancelled: 'bg-red-200 text-red-800',
  };

export default function Shoppingcart() {
    return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Shopping Cart" />
      <div className="p-6 space-y-6">

        {cartItems.map((item) => (
          <div
            key={item.id}
            className="flex items-center justify-between border p-4 rounded-lg shadow-sm"
            >
              <div className="flex items-center gap-4">
                <img
                src={item.image}
                alt={item.name}
                className="w-20 h-20 object-cover rounded-md"
                />
              <div>
                <h2 className="text-lg font-semibold">{item.name}</h2>
                <div
                  className={`inline-block px-2 py-1 text-sm rounded ${statusColor[item.status]}`}
                >
                  {item.status}
                  </div>
                </div>
              </div>

            <div className="text-right space-y-2">
              <div className="text-xl font-bold">{item.price}</div>
              <div className="flex gap-2">
                <button className="text-white rounded px-4 py-2 hover-redbg-1 hover:hover-redbg-2">Scan</button>
                <button className="text-white rounded px-4 py-2 hover-redbg-1 hover:hover-redbg-2">Bayar</button>
                <button className="text-[var(--destructive)] rounded border-3 border-[var(--destructive)] px-4 py-2 hover-redbg-1 hover-whitebg-1 hover:hover-whitebg-2">Batal</button>
                
              </div>
            </div>
          </div>
        ))}
      </div>
    </AppLayout>
  );
}