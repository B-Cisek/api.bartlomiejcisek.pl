import "dotenv/config";
import { serve } from "@hono/node-server";
import { Hono } from "hono";
import { cors } from "hono/cors";
import { EmailService } from "./services/email.service.js";
import { zValidator } from "@hono/zod-validator";
import { z } from "zod";
import { verifyRecaptcha } from "./services/recaptcha.service.js";

const contactSchema = z.object({
  name: z.string().min(1),
  email: z.email(),
  message: z.string().min(1),
  recaptcha_token: z.string(),
});

const app = new Hono();

app.use(
  "*",
  cors({
    origin: process.env.FRONTEND_HOST || "http://localhost:5173",
  })
);

app.get("/api", (c) => {
  return c.json({ status: "ok" });
});

app.post("/api/contact", zValidator("json", contactSchema), async (c) => {
  const validated = c.req.valid("json");

  try {
    const isRecaptchaValid = await verifyRecaptcha(validated.recaptcha_token);

    if (!isRecaptchaValid) {
      return c.json({ message: "recaptcha failed" }, 400);
    }
  } catch (error) {
    console.error("reCAPTCHA verification error:", error);
    return c.json({ message: "recaptcha verification failed" }, 400);
  }

  const data = {
    name: validated.name,
    email: validated.email,
    message: validated.message,
  };

  new EmailService().send(data).catch((error: any) => {
    console.error("Failed to send email:", error);
  });

  return c.json({ message: "email queued" });
});

serve(
  {
    fetch: app.fetch,
    port: Number(process.env.APP_PORT),
  },
  (info) => {
    console.log(
      `Server is running on http://${process.env.APP_HOST}:${process.env.APP_PORT}`
    );
  }
);
