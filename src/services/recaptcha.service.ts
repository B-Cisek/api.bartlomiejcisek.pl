export async function verifyRecaptcha(token: string): Promise<boolean> {
  const params = new URLSearchParams({
    secret: process.env.RECAPTCHA_SECRET_KEY as string,
    response: token,
  });

  const response = await fetch(process.env.RECAPTCHA_VERIFY_URL as string, {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: params.toString(),
  });

  if (!response.ok) {
    throw new Error("Failed to verify reCAPTCHA");
  }

  const data = await response.json();
  return data.success;
}
