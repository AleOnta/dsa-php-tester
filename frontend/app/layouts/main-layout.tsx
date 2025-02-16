import { Outlet, Link } from "react-router";
import Header from "./header";
import Footer from "./footer";

export default function MainLayout() {
    return (
        <>
            <Header/>
            <div className="lg:container">
                <Outlet/>
            </div>
            <Footer/>
        </>
    );
}