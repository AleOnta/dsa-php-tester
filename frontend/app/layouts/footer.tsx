export default function Footer() {
    return (
        <footer className="footer text-center absolute bottom-0 w-full bg-slate-700 text-slate-300 p-4">
            <aside>
                <p>Copyright © {new Date().getFullYear()} - All right reserved by AleOnta</p>
            </aside>
        </footer>
    );
}