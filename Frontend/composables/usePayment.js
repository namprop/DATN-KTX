import useAxios from "@/composables/useAxios"

export const usePayment = () => {
  const api = useAxios()

  const createVNPayPayment = async (paymentId) => {
    try {
      const res = await api.post("/vnpay/create", {
        payment_id: paymentId,
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
