import { Head } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Wishlist', href: '/cart/wishlist' },
];

// Dummy data wishlist
  const wishlistItems = [
    {
      id: 1,
      name: 'Ayam Geprek',
      category: 'Makanan',
      image: '/images/makanan/AyamGeprek.jpg',
      price: 12000,
      discount: 10,
    },
    {
      id: 2,
      name: 'Es Kopi Susu',
      category: 'Minuman',
      image: '/images/minuman/KopiSusu.jpg',
      price: 9000,
      discount: 0,
    },
    {
      id: 3,
      name: 'Paket Data 10Gb',
      category: 'Lainnya',
      image: '/images/lainlain/paketdata10gb.jpg',
      price: 25000,
      discount: 28,
    },
  ];

  const formatCurrency = (value: number) =>
    `Rp ${value.toLocaleString('id-ID', { minimumFractionDigits: 0 })}`;


export default function Wishlist() {
    return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="My Wishlist" />
      <div className="p-6 space-y-6">

        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          {wishlistItems.map((item) => {
            const discountedPrice = item.price * (1 - item.discount / 100);

            return (
              <div key={item.id} className="border rounded-lg shadow-sm overflow-hidden">
                <img
                  src={item.image}
                  alt={item.name}
                  className="w-full h-60 object-cover"
                />
                <div className="p-4 space-y-2">
                  <div className="text-sm text-muted-foreground">{item.category}</div>
                  <h2 className="text-lg font-semibold">{item.name}</h2>

                  {item.discount > 0 ? (
                    <div className="flex items-center gap-2">
                      <span className="text-[var(--destructive)] font-bold">
                        {formatCurrency(discountedPrice)}
                      </span>
                      <span className="line-through text-gray-500 text-sm">
                        {formatCurrency(item.price)}
                      </span>
                      <span className="text-[var(--destructive)] bg-red-100 text-xs px-2 py-1 rounded">
                        {item.discount}% off
                      </span>
                    </div>
                  ) : (
                    <div className="text-[var(--destructive)] font-bold">
                      {formatCurrency(item.price)}
                    </div>
                  )}

                  <button className="mt-2 w-full text-white rounded-md py-2 hover-redbg-1 hover:hover-redbg-2">Tambah</button>
                  <button className="mt-2 w-full text-[var(--destructive)] rounded-md border-3 border-[var(--destructive)] py-2 hover-whitebg-1 hover:hover-whitebg-2">Hapus</button>
                </div>
              </div>
            );
          })}
        </div>
      </div>
    </AppLayout>
  );
}