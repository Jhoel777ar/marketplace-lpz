import NextAuth, { NextAuthOptions, DefaultSession } from "next-auth";
import CredentialsProvider from "next-auth/providers/credentials";
import { PrismaClient } from "@/generated/prisma/client";
import bcrypt from "bcryptjs";

declare module "next-auth" {
  interface User {
    id: string;
    name: string;
    rol: string;
  }

  interface Session {
    user: {
      id: string;
      name: string;
      rol: string;
    } & DefaultSession["user"];
  }
}

declare module "next-auth/jwt" {
  interface JWT {
    id: string;
    rol: string;
  }
}

const prisma = new PrismaClient();

export const authOptions: NextAuthOptions = {
  providers: [
    CredentialsProvider({
      name: "Credentials",
      credentials: {
        correo: { label: "Correo", type: "email" },
        contraseña: { label: "Contraseña", type: "password" },
      },
      async authorize(credentials) {
        if (!credentials?.correo || !credentials?.contraseña) return null;

        const user = await prisma.usuario.findUnique({
          where: { correo: credentials.correo },
        });
        if (!user || !bcrypt.compareSync(credentials.contraseña, user.contraseña)) return null;

        return { id: user.id.toString(), name: user.nombre, rol: user.rol };
      },
    }),
  ],
  pages: { signIn: "/login" },
  session: { strategy: "jwt" as const },
  callbacks: {
    async jwt({ token, user }) {
      if (user) {
        token.id = user.id;
        token.rol = user.rol;
      }
      return token;
    },
    async session({ session, token }) {
      if (token && session.user) {
        session.user.id = token.id;
        session.user.rol = token.rol;
      }
      return session;
    },
  },
  secret: process.env.NEXTAUTH_SECRET,
};

const handler = NextAuth(authOptions);
export { handler as GET, handler as POST };