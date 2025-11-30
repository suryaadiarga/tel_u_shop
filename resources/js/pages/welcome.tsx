import { dashboard, login, register } from '@/routes';
import { type SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';

export default function Welcome() {
    const { auth } = usePage<SharedData>().props;
    
    return (
        <>
            <Head title="Welcome">
                <link rel="preconnect" href="https://fonts.bunny.net" />
                <link
                    href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600"
                    rel="stylesheet"
                />
            </Head>
            <div
                style={{
                    backgroundImage: "url('/images/welcome/redwhitebg.jpg')",
                    backgroundRepeat: 'no-repeat',
                    backgroundPosition: 'bottom center',
                    backgroundSize: 'cover',
                }}
                className="flex min-h-screen flex-col items-center justify-center p-6 text-[var(--foreground)]"
            >
                <div className="w-full max-w-sm rounded-lg bg-[var(--card)] p-8 shadow-lg flex flex-col items-center">
                    <h1 className="text-3xl font-bold mb-2 text-center text-[var(--sidebar-primary)]">Tel U Shop</h1>
                    <p className="text-center text-[var(--muted-foreground)] mb-6">
                        Selamat datang di Tel U Shop, anda bisa membeli apa yang anda cari disini.
                    </p>
                </div>
                <div className="mt-8 flex flex-col w-full max-w-sm gap-3">
                    {auth.user ? (
                        <Link
                            href={dashboard()}
                            className="w-full rounded-md bg-[var(--primary)] px-5 py-2 text-[var(--primary-foreground)] text-center font-medium hover-redbg-1 hover:hover-redbg-2"
                        >
                            Dashboard
                        </Link>
                    ) : (
                        <>
                            <Link
                                href={login()}
                                className="w-full rounded-md bg-[var(--primary)] px-5 py-2 text-[var(--primary-foreground)] text-center font-medium hover-redbg-1 hover:hover-redbg-2"
                            >
                                Login
                            </Link>
                            <Link
                                href={register()}
                                className="w-full rounded-md border border-[var(--destructive)] bg-[var(--primary-foreground)] px-5 py-2 text-[var(--destructive)] text-center font-medium hover:brightness-80"
                            >
                                Register
                            </Link>
                        </>
                    )}
                </div>
            </div>
        </>
    );
}