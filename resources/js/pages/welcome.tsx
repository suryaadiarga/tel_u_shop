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
            <div className="flex min-h-screen flex-col items-center justify-center bg-[#FDFDFC] p-6 text-[#1b1b18] dark:bg-[#0a0a0a]">
                <div className="w-full max-w-sm rounded-lg bg-white p-8 shadow-lg flex flex-col items-center dark:bg-[#161615] dark:text-[#EDEDEC]">
                    <h1 className="text-3xl font-bold mb-2 text-center">Koperasi Tel-U</h1>
                    <p className="text-center text-[#706f6c] mb-6 dark:text-[#A1A09A]">
                        Selamat datang di Koperasi Tel-U, anda bisa membeli apa yang anda cari disini.
                    </p>
                </div>
                <div className="mt-8 flex flex-col w-full max-w-sm gap-3">
                    {auth.user ? (
                        <Link
                            href={dashboard()}
                            className="w-full rounded-md bg-[#bf1206] px-5 py-2 text-white text-center font-medium hover:bg-[#a00e05] dark:bg-[#EDEDEC] dark:text-[#1C1C1A] dark:hover:bg-[#d6d6d6]"
                        >
                            Dashboard
                        </Link>
                    ) : (
                        <>
                            <Link
                                href={login()}
                                className="w-full rounded-md bg-[#bf1206] px-5 py-2 text-white text-center font-medium hover:bg-[#a00e05] dark:bg-[#EDEDEC] dark:text-[#1C1C1A] dark:hover:bg-[#d6d6d6]"
                            >
                                Login
                            </Link>
                            <Link
                                href={register()}
                                className="w-full rounded-md border border-[#bf1206] px-5 py-2 text-[#bf1206] text-center font-medium hover:bg-[#f5f5f5] dark:border-[#EDEDEC] dark:text-[#EDEDEC] dark:hover:bg-[#232323]"
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