"use client";

import { cn } from "@/lib/utils";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Skeleton } from "@/components/ui/skeleton";
import { Toaster } from "@/components/ui/sonner";
import { toast } from "sonner";
import { useState } from "react";
import { signIn } from "next-auth/react";
import { useRouter } from "next/navigation";

export function LoginForm({
  className,
  ...props
}: React.ComponentProps<"form">) {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [isLoading, setIsLoading] = useState(false);
  const router = useRouter();

  const validateInputs = () => {
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email))
      return "Correo inválido";
    if (!password || password.length < 6)
      return "La contraseña debe tener al menos 6 caracteres";
    return null;
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsLoading(true);

    const validationError = validateInputs();
    if (validationError) {
      toast.error(validationError, { duration: 2000 });
      setIsLoading(false);
      return;
    }

    toast("Verificando credenciales...", { duration: 1000 });

    const res = await signIn("credentials", {
      redirect: false,
      correo: email,
      contraseña: password,
    });

    if (res?.ok) {
      toast("Redirigiendo...", { duration: 1000 });
      router.push("/dashboard");
    } else {
      toast.error(
        res?.error === "CredentialsSignin"
          ? "Credenciales incorrectas"
          : res?.error || "Error al iniciar sesión",
        {
          duration: 2000,
        }
      );
      setIsLoading(false);
    }
  };

  return (
    <form
      onSubmit={handleSubmit}
      className={cn(
        "flex flex-col gap-6 max-w-md w-full mx-auto p-6 bg-black text-white rounded-lg shadow-lg",
        className
      )}
      {...props}
    >
      <Toaster />
      <div className="flex flex-col items-center gap-2 text-center">
        <h1 className="text-2xl font-bold tracking-tight">Inicia sesión</h1>
        <p className="text-gray-400 text-sm text-balance">
          Accede a tu cuenta en Tu Ex Market
        </p>
      </div>
      {isLoading ? (
        <div className="grid gap-6">
          <Skeleton className="h-12 w-full rounded-md bg-gray-800" />
          <Skeleton className="h-12 w-full rounded-md bg-gray-800" />
        </div>
      ) : (
        <div className="grid gap-6">
          <div className="grid gap-3">
            <Label htmlFor="email" className="text-sm font-medium">
              Correo
            </Label>
            <Input
              id="email"
              type="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              placeholder="m@example.com"
              required
              disabled={isLoading}
              className="h-12 bg-gray-900 text-white border-gray-700 focus:ring-2 focus:ring-white transition-all duration-300 placeholder:text-gray-500"
            />
          </div>
          <div className="grid gap-3">
            <div className="flex items-center">
              <Label htmlFor="password" className="text-sm font-medium">
                Contraseña
              </Label>
              <a
                href="#"
                className="ml-auto text-sm text-gray-400 underline-offset-2 hover:underline hover:text-white"
              >
                ¿Olvidaste tu contraseña?
              </a>
            </div>
            <Input
              id="password"
              type="password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              required
              disabled={isLoading}
              className="h-12 bg-gray-900 text-white border-gray-700 focus:ring-2 focus:ring-white transition-all duration-300 placeholder:text-gray-500"
              placeholder="Ingresa tu contraseña"
            />
          </div>
          <Button
            type="submit"
            className="w-full h-12 text-base font-semibold bg-white text-black hover:bg-gray-200 transition-all duration-300"
            disabled={isLoading}
          >
            {isLoading ? "Procesando..." : "Iniciar sesión"}
          </Button>
        </div>
      )}
      <div className="after:border-gray-700 relative text-center text-sm after:absolute after:inset-0 after:top-1/2 after:z-0 after:flex after:items-center after:border-t">
        <span className="bg-black text-gray-400 relative z-10 px-2">
          O continúa con
        </span>
      </div>
      <div className="grid grid-cols-3 gap-4">
        <Button
          variant="outline"
          type="button"
          className="w-full bg-gray-900 text-white border-gray-700 hover:bg-gray-800"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            className="w-5 h-5"
          >
            <path
              d="M12.48 10.92v3.28h7.84c-.24 1.84-.853 3.187-1.787 4.133-1.147 1.147-2.933 2.4-6.053 2.4-4.827 0-8.6-3.893-8.6-8.72s3.773-8.72 8.6-8.72c2.6 0 4.507 1.027 5.907 2.347l2.307-2.307C18.747 1.44 16.133 0 12.48 0 5.867 0 .307 5.387.307 12s5.56 12 12.173 12c3.573 0 6.267-1.173 8.373-3.36 2.16-2.16 2.84-5.213 2.84-7.667 0-.76-.053-1.467-.173-2.053H12.48z"
              fill="currentColor"
            />
          </svg>
          <span className="sr-only">Iniciar con Google</span>
        </Button>
      </div>
      <div className="text-center text-sm">
        ¿No tienes cuenta?{" "}
        <a
          href="/register"
          className="underline underline-offset-4 text-gray-400 hover:text-white"
        >
          Regístrate
        </a>
      </div>
    </form>
  );
}
