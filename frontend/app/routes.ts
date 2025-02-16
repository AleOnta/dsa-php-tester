import { type RouteConfig, index, layout, route } from "@react-router/dev/routes";

export default [
    layout("layouts/main-layout.tsx", [
        index("routes/home.tsx"),
    ])
] satisfies RouteConfig;
