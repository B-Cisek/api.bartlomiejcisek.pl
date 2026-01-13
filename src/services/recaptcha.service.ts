export async function verifyRecaptcha(token: string): Promise<boolean> {
  const response = await fetch(process.env.RECAPTCHA_VERIFY_URL as string, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      secret: process.env.RECAPTCHA_SECRET_KEY as string,
      response: token,
    }),
  });

  if (!response.ok) {
    throw new Error("Failed to verify reCAPTCHA");
  }

  const data = await response.json();
  return data.success;
}
