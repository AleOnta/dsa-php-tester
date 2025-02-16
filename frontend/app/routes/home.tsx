import { useEffect, useState } from "react";
import type { Route } from "./+types/home";

export function meta({}: Route.MetaArgs) {
  return [
    { title: "New React Router App" },
    { name: "description", content: "Welcome to React Router!" },
  ];
}

export default function Home() {
  
  const [data, setData] = useState<string | null>(null);
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {

    const fetchData = async () => {
      try {
        const response = await fetch("http://localhost:8000/api/v1/auth/login", {
          'method': 'POST'
        });
        const data = await response.json();
        console.log(data);
      } catch (error) {
        console.error("Error has occurred: ", error);
      } finally {
        setIsLoading(false);
      }
    }

    fetchData();

  }, [])

  return (
    <h2 className="text-white">Test</h2>
  );
}
