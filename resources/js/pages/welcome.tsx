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
            <div className="flex min-h-screen flex-col items-center justify-center bg-background p-6 text-foreground">
                <div className="w-full max-w-sm rounded-2xl bg-card border border-border/70 p-8 shadow-sm flex flex-col items-center">
                    <h1 className="text-3xl font-bold mb-2 text-center">Koperasi Tel-U</h1>
                    <p className="text-center text-muted-foreground mb-6">
                        Selamat datang di Koperasi Tel-U, anda bisa membeli apa yang anda cari disini.
                    </p>
                </div>
                <div className="mt-8 flex flex-col w-full max-w-sm gap-3">
                    {auth.user ? (
                        <Link
                            href={dashboard()}
                            className="w-full rounded-lg bg-primary px-5 py-2 text-primary-foreground text-center font-medium hover:bg-primary/90"
                        >
                            Dashboard
                        </Link>
                    ) : (
                        <>
                            <Link
                                href={login()}
                                className="w-full rounded-lg bg-primary px-5 py-2 text-primary-foreground text-center font-medium hover:bg-primary/90"
                            >
                                Login
                            </Link>
                            <Link
                                href={register()}
                                className="w-full rounded-lg border border-primary/40 px-5 py-2 text-primary text-center font-medium hover:bg-primary/10"
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
