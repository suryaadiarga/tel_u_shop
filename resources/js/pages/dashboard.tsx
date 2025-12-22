import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/' },
];

// --- dummy data (nanti tinggal ganti dari props/API) ---
const banners = [
    { id: 1, title: 'Flash Sale 11.11', subtitle: 'Diskon sampai 70%', bg: '#bf1206' },
    { id: 2, title: 'Voucher Makanan', subtitle: 'Hemat tiap hari', bg: '#1d4ed8' },
    { id: 3, title: 'Pulsa & Data', subtitle: 'Harga mahasiswa', bg: '#10b981' },
];

const categories = [
    { id: 1, name: 'Pulsa', icon: '📱' },
    { id: 2, name: 'Paket Data', icon: '📶' },
    { id: 3, name: 'Makanan', icon: '🍔' },
    { id: 4, name: 'Minuman', icon: '🥤' },
    { id: 5, name: 'Elektronik', icon: '💡' },
    { id: 6, name: 'Fashion', icon: '👕' },
    { id: 7, name: 'Tiket', icon: '🎫' },
    { id: 8, name: 'Lainnya', icon: '✨' },
];

const flashSale = [
    { id: 1, name: 'Paket Data 10GB', price: 18000, cut: 25000, badge: '–28%' },
    { id: 2, name: 'Ayam Geprek', price: 12000, cut: 17000, badge: '–29%' },
    { id: 3, name: 'Es Kopi Susu', price: 9000, cut: 14000, badge: '–35%' },
    { id: 4, name: 'Pulsa 25k', price: 23000, cut: 25000, badge: '–8%' },
];

const recommended = [
    { id: 10, name: 'Charger 20W', price: 39000 },
    { id: 11, name: 'Headset Basic', price: 59000 },
    { id: 12, name: 'T-shirt Kampus', price: 75000 },
    { id: 13, name: 'Mouse Wireless', price: 89000 },
    { id: 14, name: 'Roti Bakar Cokelat', price: 12000 },
    { id: 15, name: 'Jas Hujan', price: 65000 },
];

const lastOrders = [
    { id: 'ORD-231023-01', title: 'Paket Data 10GB', status: 'Sukses', amount: 18000, at: '23 Okt, 19:10' },
    { id: 'ORD-231023-02', title: 'Es Kopi Susu', status: 'Diproses', amount: 9000, at: '23 Okt, 19:02' },
    { id: 'ORD-231023-03', title: 'Pulsa 25k', status: 'Sukses', amount: 23000, at: '23 Okt, 18:41' },
];

export default function Dashboard() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />

            {/* Top bar: search + notifikasi */}
            <div className="flex items-center gap-3">
                <div className="relative flex-1">
                    <input
                        type="text"
                        placeholder="Cari item, voucher, toko…"
                        className="w-full rounded-xl bg-card text-foreground placeholder:text-muted-foreground border border-border/70 pl-10 pr-4 py-2.5 shadow-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/40"
                    />
                    <svg
                        className="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground"
                        width="18" height="18" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24"
                    >
                        <circle cx="11" cy="11" r="8" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                </div>

                <button className="relative rounded-full border border-primary/30 bg-card p-2 shadow-sm hover:bg-accent/60 transition">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" className="text-primary" stroke="currentColor" strokeWidth="2">
                        <path d="M18 8a6 6 0 10-12 0c0 7-3 9-3 9h18s-3-2-3-9" />
                        <path d="M13.73 21a2 2 0 01-3.46 0" />
                    </svg>
                    <span className="absolute -top-1 -right-1 h-4 w-4 rounded-full bg-primary text-primary-foreground text-[10px] flex items-center justify-center">3</span>
                </button>
            </div>

            {/* Wallet / quick actions */}
            <div className="mt-4 grid gap-3 md:grid-cols-3">
                <div className="rounded-2xl bg-card border border-border/70 shadow-sm p-4 flex items-center justify-between">
                    <div className="flex items-center gap-3">
                        <div className="h-10 w-10 rounded-xl bg-primary/10 flex items-center justify-center">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" className="text-primary" strokeWidth="2">
                                <rect x="2" y="7" width="20" height="14" rx="4" />
                                <path d="M16 3v4M8 3v4" />
                            </svg>
                        </div>
                        <div>
                            <p className="text-sm text-muted-foreground">Saldo</p>
                            <p className="text-lg font-semibold text-primary">Rp 45.000</p>
                        </div>
                    </div>
                    <Link href="/topup" className="text-sm font-medium text-primary hover:underline">Top Up</Link>
                </div>

                <div className="rounded-2xl bg-card border border-border/70 shadow-sm p-4 flex items-center justify-between">
                    <div className="flex items-center gap-3">
                        <div className="h-10 w-10 rounded-xl bg-primary/10 flex items-center justify-center">
                            <span className="text-primary text-lg">🎁</span>
                        </div>
                        <div>
                            <p className="text-sm text-muted-foreground">Voucher</p>
                            <p className="text-lg font-semibold text-foreground">2 aktif</p>
                        </div>
                    </div>
                    <Link href="/vouchers" className="text-sm font-medium text-primary hover:underline">Lihat</Link>
                </div>

                <div className="rounded-2xl bg-card border border-border/70 shadow-sm p-4 flex items-center justify-between">
                    <div className="flex items-center gap-3">
                        <div className="h-10 w-10 rounded-xl bg-primary/10 flex items-center justify-center">
                            <span className="text-primary text-lg">⭐</span>
                        </div>
                        <div>
                            <p className="text-sm text-muted-foreground">Points</p>
                            <p className="text-lg font-semibold text-foreground">120 pts</p>
                        </div>
                    </div>
                    <Link href="/rewards" className="text-sm font-medium text-primary hover:underline">Tukar</Link>
                </div>
            </div>

            {/* Banner carousel sederhana */}
            <div className="mt-8">
                <div className="overflow-x-auto no-scrollbar">
                    <div className="flex gap-3 w-max">
                        {banners.map(b => (
                            <div key={b.id} className="min-w-[280px] md:min-w-[360px] rounded-2xl text-white p-5 shadow-sm ring-1 ring-white/15"
                                style={{ background: b.bg }}>
                                <p className="text-sm/none opacity-85">Promo</p>
                                <p className="text-xl md:text-2xl font-semibold">{b.title}</p>
                                <p className="opacity-90 mt-1">{b.subtitle}</p>
                                <Link href="/promo" className="mt-3 inline-block rounded-lg bg-white/15 hover:bg-white/25 px-3 py-1 text-sm">
                                    Lihat Detail
                                </Link>
                            </div>
                        ))}
                    </div>
                </div>
            </div>

            {/* Kategori */}
            <section className="mt-8">
                <div className="flex items-center justify-between mb-3">
                    <h3 className="text-lg font-semibold">Kategori</h3>
                    <Link href="/categories" className="text-sm text-muted-foreground hover:text-foreground">Lihat semua</Link>
                </div>

                <div className="overflow-x-auto no-scrollbar">
                    <div className="flex gap-3 w-max">
                        {categories.map(cat => (
                            <Link
                                key={cat.id}
                                href={`/category/${encodeURIComponent(cat.name.toLowerCase())}`}
                                className="w-24 shrink-0 rounded-2xl bg-card border border-border/70 shadow-sm p-3 flex flex-col items-center hover:shadow-md hover:border-primary/20 transition"
                            >
                                <div className="h-12 w-12 rounded-xl bg-muted flex items-center justify-center text-xl text-primary">
                                    {cat.icon}
                                </div>
                                <p className="mt-2 text-sm text-center">{cat.name}</p>
                            </Link>
                        ))}
                    </div>
                </div>
            </section>

            {/* Flash Sale */}
            <section className="mt-8">
                <div className="flex items-center justify-between mb-3">
                    <h3 className="text-lg font-semibold">Flash Sale <span className="ml-2 text-xs rounded bg-primary/10 text-primary px-2 py-0.5">Berakhir 02:15:37</span></h3>
                    <Link href="/flash-sale" className="text-sm text-muted-foreground hover:text-foreground">Lihat semua</Link>
                </div>

                <div className="grid grid-cols-2 md:grid-cols-4 gap-3">
                    {flashSale.map(item => (
                        <div key={item.id} className="rounded-2xl bg-card border border-border/70 shadow-sm p-3 hover:shadow-md hover:border-primary/20 transition">
                            <div className="aspect-[4/3] rounded-xl bg-gradient-to-br from-gray-100 to-gray-200 mb-3 relative overflow-hidden">
                                <span className="absolute left-2 top-2 text-[11px] bg-primary text-primary-foreground rounded px-1.5 py-0.5">{item.badge}</span>
                            </div>
                            <p className="text-sm line-clamp-2">{item.name}</p>
                            <div className="mt-1 flex items-baseline gap-2">
                                <p className="font-semibold text-primary">Rp {item.price.toLocaleString('id-ID')}</p>
                                <p className="text-xs text-gray-400 line-through">Rp {item.cut.toLocaleString('id-ID')}</p>
                            </div>
                            <button className="mt-2 w-full rounded-lg bg-primary text-primary-foreground text-sm py-2 hover:bg-primary/90">Tambah</button>
                        </div>
                    ))}
                </div>
            </section>

            {/* Rekomendasi */}
            <section className="mt-8">
                <div className="flex items-center justify-between mb-3">
                    <h3 className="text-lg font-semibold">Rekomendasi untuk kamu</h3>
                    <Link href="/products" className="text-sm text-muted-foreground hover:text-foreground">Lihat semua</Link>
                </div>

                <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                    {recommended.map(p => (
                        <div key={p.id} className="rounded-2xl bg-card border border-border/70 shadow-sm p-3 hover:shadow-md hover:border-primary/20 transition">
                            <div className="aspect-square rounded-xl bg-gradient-to-br from-gray-100 to-gray-200 mb-3" />
                            <p className="text-sm line-clamp-2">{p.name}</p>
                            <p className="mt-1 font-semibold text-primary">Rp {p.price.toLocaleString('id-ID')}</p>
                            <button className="mt-2 w-full rounded-lg border border-primary/40 text-primary text-sm py-2 hover:bg-primary/10">Tambah</button>
                        </div>
                    ))}
                </div>
            </section>

            {/* Riwayat / Pesanan Terakhir */}
            <section className="mt-8">
                <div className="flex items-center justify-between mb-3">
                    <h3 className="text-lg font-semibold">Pesanan Terakhir</h3>
                    <Link href="/orders" className="text-sm text-muted-foreground hover:text-foreground">Lihat semua</Link>
                </div>
                <div className="rounded-2xl bg-card border border-border/70 shadow-sm divide-y">
                    {lastOrders.map(o => (
                        <div key={o.id} className="p-4 flex items-center justify-between">
                            <div>
                                <p className="text-sm font-medium">{o.title}</p>
                                <p className="text-xs text-muted-foreground">{o.id} • {o.at}</p>
                            </div>
                            <div className="text-right">
                                <p className="text-sm font-semibold text-foreground">Rp {o.amount.toLocaleString('id-ID')}</p>
                                <span className={[
                                    'inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium',
                                    o.status === 'Sukses' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600'
                                ].join(' ')}>{o.status}</span>
                            </div>
                        </div>
                    ))}
                </div>
            </section>

            {/* spacing bottom */}
            <div className="h-6" />
        </AppLayout>
    );
}

/* util: sembunyikan scrollbar horizontal di section geser */
declare global {
    interface CSSStyleDeclaration {
        scrollbarWidth?: string;
    }
}
