"use client";

import { cn } from "@/lib/utils";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Toaster } from "@/components/ui/sonner";
import { toast } from "sonner";
import { useState } from "react";
import { useRouter } from "next/navigation";

export function RegisterForm({
  className,
  ...props
}: React.ComponentProps<"form">) {
  const [nombre, setNombre] = useState("");
  const [correo, setCorreo] = useState("");
  const [contraseña, setContraseña] = useState("");
  const [error, setError] = useState("");
  const [isLoading, setIsLoading] = useState(false);
  const router = useRouter();

  const validateInputs = () => {
    if (!nombre || nombre.length < 2)
      return "El nombre debe tener al menos 2 caracteres";
    if (!correo || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo))
      return "Correo inválido";
    if (!contraseña || contraseña.length < 6)
      return "La contraseña debe tener al menos 6 caracteres";
    return null;
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError("");
    setIsLoading(true);

    const validationError = validateInputs();
    if (validationError) {
      setError(validationError);
      setIsLoading(false);
      return;
    }

    toast("Validando datos...", { duration: 1000 });

    try {
      const res = await fetch("/api/register", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          nombre,
          correo,
          contraseña,
          rol: correo.endsWith(".edu") ? "Emprendedor" : "Cliente",
        }),
      });

      if (res.ok) {
        toast("¡Validación exitosa! Redirigiendo...", { duration: 1000 });
        router.push("/dashboard");
      } else {
        setError(await res.text());
        setIsLoading(false);
      }
    } catch {
      setError("Error en el servidor");
      setIsLoading(false);
    }
  };

  return (
    <form
      onSubmit={handleSubmit}
      className={cn("flex flex-col gap-6", className)}
      {...props}
    >
      <Toaster />
      <div className="flex flex-col items-center gap-2 text-center">
        <h1 className="text-2xl font-bold">Regístrate</h1>
        <p className="text-muted-foreground text-sm text-balance">
          Crea tu cuenta en Tu ex Market
        </p>
      </div>

      <div className="grid gap-6">
        {error && (
          <p className="text-red-500 text-sm text-center">{error}</p>
        )}

        <div className="grid gap-3">
          <Label htmlFor="nombre">Nombre</Label>
          <Input
            id="nombre"
            value={nombre}
            onChange={(e) => setNombre(e.target.value)}
            required
            disabled={isLoading}
            placeholder="Ingresa tu nombre"
          />
        </div>

        <div className="grid gap-3">
          <Label htmlFor="correo">Correo</Label>
          <Input
            id="correo"
            type="email"
            value={correo}
            onChange={(e) => setCorreo(e.target.value)}
            required
            disabled={isLoading}
            placeholder="m@example.com o m@edu.com"
          />
        </div>

        <div className="grid gap-3">
          <div className="flex items-center">
            <Label htmlFor="contraseña">Contraseña</Label>
          </div>
          <Input
            id="contraseña"
            type="password"
            value={contraseña}
            onChange={(e) => setContraseña(e.target.value)}
            required
            disabled={isLoading}
            placeholder="Ingresa tu contraseña"
          />
        </div>

        <Button type="submit" className="w-full" disabled={isLoading}>
          {isLoading ? "Procesando..." : "Registrarse"}
        </Button>
      </div>

      <div className="text-center text-sm">
        ¿Ya tienes cuenta?{" "}
        <a href="/login" className="underline underline-offset-4">
          Inicia sesión
        </a>
      </div>
    </form>
  );
}