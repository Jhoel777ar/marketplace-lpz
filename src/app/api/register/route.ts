import { NextResponse } from "next/server";
import { PrismaClient } from "@/generated/prisma/client";
import bcrypt from "bcryptjs";

const prisma = new PrismaClient({
  datasources: {
    db: {
      url: process.env.DATABASE_URL,
    },
  },
});

export async function POST(request: Request) {
  try {
    const { nombre, correo, contraseña, rol } = await request.json();

    if (!nombre || !correo || !contraseña || !rol) {
      return NextResponse.json("Faltan campos", { status: 400 });
    }

    const exists = await prisma.usuario.findUnique({
      where: { correo },
      select: { id: true },
    });
    if (exists) {
      return NextResponse.json("Correo ya registrado", { status: 400 });
    }
    const hashed = await bcrypt.hash(contraseña, 8);

    const user = await prisma.$transaction(async (tx) => {
      const newUser = await tx.usuario.create({
        data: { nombre, correo, contraseña: hashed, rol, estado: "Activo" },
        select: { id: true, rol: true },
      });

      if (rol === "Cliente") {
        await tx.cliente.create({
          data: { id: newUser.id, nivelCliente: "Básico" },
        });
      } else if (rol === "Emprendedor") {
        await tx.emprendedor.create({
          data: { id: newUser.id, carreraUniversitaria: "", semestre: 0, documento: "" },
        });
      }

      return newUser;
    });

    return NextResponse.json({ message: "Registrado", userId: user.id }, { status: 201 });
  } catch (error) {
    console.error(error);
    return NextResponse.json("Error en el servidor", { status: 500 });
  } finally {
    await prisma.$disconnect();
  }
}