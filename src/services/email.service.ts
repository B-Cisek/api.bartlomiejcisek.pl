import nodemailer from "nodemailer";
import type { Transporter } from "nodemailer";

type ContactForm = {
  name: string;
  email: string;
  message: string;
};

export class EmailService {
  async send(data: ContactForm) {
    const transporter = this.createTransporter();

    const response = await transporter.sendMail({
      to: process.env.MAIL_TO,
      subject: "Contact Form Mail",
      text: `Wiadomość od: ${data.name} <${data.email}> \n\n ${data.message}`,
    });

    console.log(response);
  }

  private createTransporter(): Transporter {
    return nodemailer.createTransport({
      host: process.env.MAIL_HOST,
      port: Number(process.env.MAIL_PORT),
      secure: true,
      auth: {
        user: process.env.MAIL_USER,
        pass: process.env.MAIL_PASS,
      },
    });
  }
}
