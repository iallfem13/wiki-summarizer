import { Link, usePage } from '@inertiajs/react'

export default function AppLayout ({ children }) {
    const { url } = usePage(); // all data passed from the server (page-specific & globally stored data)

    return (
        <div className="main-layout">
            { /* Universal Navigation */}
            <nav className="nav-bar">
                <div className="navbar-brand">
                    {/* left side: logo */}
                    <Link href="/summarize" className="text-2xl font-bold tracking-tight text-gray-900">
                        <img src="/logo.png" alt="Logo" className="nav-logo" /> beep boop auditor
                    </Link>
                </div>

                {/* nav menu links group */}
                <div className="flex items-center space-x-2">
                    <Link className={`nav-link ${url === '/summarize' ? 'active' : ''}`}
                        href="/summarize">Summarize Text</Link>
                    <Link className={`nav-link ${url === '/documents' ? 'active' : ''}`}
                        href="/documents">Summarized Results</Link>
                </div>
            </nav>

            {/* Main content */}
            <main className="main-content">
                {children}
            </main>

            {/* Footer */}
            <footer className="footer">
                &copy; Beep Boop Auditor. All rights reserved.
            </footer>
        </div>
    );
}
