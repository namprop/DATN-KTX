import axios from "axios"

export const usePayment = () => {
  const createVNPayPayment = async (amount, description) => {
    try {
      const res = await axios.post("http://localhost:8000/api/vnpay/create", {
        amount,
        description,
      })
      if (res.data.payment_url) {
        window.location.href = res.data.payment_url
      }
    } catch (error) {
      console.error("VNPAY error:", error)
    }
  }

  return { createVNPayPayment }
}
