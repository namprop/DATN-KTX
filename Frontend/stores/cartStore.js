import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useCartStore = defineStore('cart', () => {
  const cart = ref([])

  const initCart = () => {
    if (import.meta.client) {
      const saved = localStorage.getItem('cart')
      if (saved) {
        try {
          cart.value = JSON.parse(saved)
        } catch {
          cart.value = []
        }
      }
    }
  }

  const saveToLocalStorage = () => {
    if (import.meta.client) {
      localStorage.setItem('cart', JSON.stringify(cart.value))
    }
  }

  const addToCart = (product) => {
    const found = cart.value.find(item => item.id === product.id)
    if (found) {
      found.quantity += 1
    } else {
      cart.value.push({
        ...product,
        // image: product.image?.[0] ?? '', 
        quantity: 1,
        price: parseInt(product.price.replace(/\D/g, ''))
      })
    }
    saveToLocalStorage()
  }

  const removeFromCart = (id) => {
    cart.value = cart.value.filter(item => item.id !== id)
    saveToLocalStorage()
  }

  const increaseQuantity = (id) => {
    const item = cart.value.find(item => item.id === id)
    if (item) {
      item.quantity++
      saveToLocalStorage()
    }
  }

const decreaseQuantity = (id) => {
  const item = cart.value.find(item => item.id === id);
  if (item) {
    if (item.quantity > 1) {
      item.quantity--;
    } else {
      item.quantity = 1; 
    }
    saveToLocalStorage();
  }
};

const totalQuantityItem = (id) => {
  const item = cart.value.find(item => item.id === id);
  return item ? item.quantity  : 1;
};

const totalPriceItem = (id) => {
  const item = cart.value.find(item => item.id === id);
  return item ? item.quantity * item.price  : 0;
};


  const clearCart = () => {
    cart.value = []
    if (import.meta.client) {
      localStorage.removeItem('cart')
    }
  }

  const totalItems = computed(() =>
    cart.value.reduce((sum, item) => sum + item.quantity, 0)
  )

  const totalPrice = computed(() =>
    cart.value.reduce((sum, item) => sum + item.quantity * item.price, 0)
  )

  return {
    cart,
    initCart,
    addToCart,
    removeFromCart,
    increaseQuantity,
    decreaseQuantity,
    clearCart,
    totalQuantityItem,
    totalPriceItem,
    totalItems,
    totalPrice
  }
})
