<template>
  <div class="payment-method-form">
    <div class="payment-type-selector">
      <button
        :class="['type-btn', paymentType === 'card' ? 'active' : '']"
        @click="paymentType = 'card'"
      >
        <i class="fas fa-credit-card"></i>
        Carte bancaire
      </button>
      <button
        :class="['type-btn', paymentType === 'paypal' ? 'active' : '']"
        @click="paymentType = 'paypal'"
      >
        <i class="fab fa-paypal"></i>
        PayPal
      </button>
    </div>

    <!-- Formulaire de carte bancaire -->
    <div v-if="paymentType === 'card'" class="card-form">
      <div id="card-element" class="card-element"></div>
      <div id="card-errors" class="error-message" role="alert"></div>

      <div class="save-card">
        <label class="checkbox-container">
          <input
            type="checkbox"
            v-model="saveCard"
          >
          <span class="checkbox-label">Sauvegarder cette carte pour les paiements futurs</span>
        </label>
      </div>
    </div>

    <!-- Formulaire PayPal -->
    <div v-else class="paypal-form">
      <div class="form-group">
        <label for="paypal-email">Email PayPal</label>
        <input
          id="paypal-email"
          v-model="paypalEmail"
          type="email"
          class="form-control"
          placeholder="votre@email.com"
          required
        >
      </div>

      <div id="paypal-button-container" class="paypal-button"></div>
    </div>

    <div class="current-method" v-if="currentMethod">
      <h4>Méthode de paiement actuelle</h4>
      <div class="method-info">
        <i :class="getPaymentIcon(currentMethod.type)"></i>
        <span v-if="currentMethod.type === 'card'">
          {{ currentMethod.card_brand }} **** {{ currentMethod.card_last_four }}
          <span class="expires">Expire le {{ formatExpiry(currentMethod.expires_at) }}</span>
        </span>
        <span v-else-if="currentMethod.type === 'paypal'">
          PayPal ({{ currentMethod.paypal_email }})
        </span>
      </div>
    </div>

    <div class="form-actions">
      <button type="button" class="btn-cancel" @click="$emit('cancel')">
        Annuler
      </button>
      <button
        type="button"
        class="btn-save"
        :disabled="!isValid"
        @click="savePaymentMethod"
      >
        Enregistrer
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PaymentMethodForm',

  props: {
    currentMethod: {
      type: Object,
      default: null
    }
  },

  data() {
    return {
      paymentType: 'card',
      stripe: null,
      card: null,
      saveCard: true,
      paypalEmail: '',
      isValid: false
    }
  },

  async mounted() {
    if (this.paymentType === 'card') {
      await this.initializeStripe()
    } else {
      await this.initializePayPal()
    }
  },

  watch: {
    async paymentType(newType) {
      if (newType === 'card') {
        await this.initializeStripe()
      } else {
        await this.initializePayPal()
      }
    }
  },

  methods: {
    async initializeStripe() {
      // Charger Stripe
      if (!this.stripe) {
        this.stripe = Stripe(process.env.MIX_STRIPE_KEY)
      }

      // Créer les éléments de carte
      const elements = this.stripe.elements()
      this.card = elements.create('card', {
        style: {
          base: {
            fontSize: '16px',
            color: '#32325d',
            '::placeholder': {
              color: '#aab7c4'
            }
          },
          invalid: {
            color: '#fa755a',
            iconColor: '#fa755a'
          }
        }
      })

      // Monter l'élément de carte
      this.card.mount('#card-element')

      // Gérer les erreurs de validation
      this.card.addEventListener('change', (event) => {
        const displayError = document.getElementById('card-errors')
        if (event.error) {
          displayError.textContent = event.error.message
          this.isValid = false
        } else {
          displayError.textContent = ''
          this.isValid = event.complete
        }
      })
    },

    async initializePayPal() {
      // Initialiser le bouton PayPal
      paypal.Buttons({
        createOrder: (data, actions) => {
          return actions.order.create({
            intent: 'CAPTURE',
            purchase_units: [{
              amount: {
                value: '1.00',
                currency_code: 'EUR'
              },
              description: 'Vérification du compte PayPal'
            }]
          })
        },
        onApprove: async (data, actions) => {
          try {
            const order = await actions.order.capture()
            this.paypalEmail = order.payer.email_address
            this.isValid = true
          } catch (error) {
            console.error('Erreur PayPal:', error)
            const displayError = document.getElementById('paypal-errors')
            if (displayError) {
              displayError.textContent = 'Une erreur est survenue lors de la vérification PayPal'
            }
          }
        },
        onError: (err) => {
          console.error('Erreur PayPal:', err)
          const displayError = document.getElementById('paypal-errors')
          if (displayError) {
            displayError.textContent = 'Une erreur est survenue avec PayPal'
          }
        }
      }).render('#paypal-button-container')
    },

    async getPayPalEmail(orderId) {
      // Récupérer l'email PayPal depuis le serveur
      const response = await this.$axios.post('/api/paypal/get-email', { order_id: orderId })
      return response.data.email
    },

    async savePaymentMethod() {
      try {
        if (this.paymentType === 'card') {
          const { token, error } = await this.stripe.createToken(this.card)
          if (error) {
            throw new Error(error.message)
          }
          await this.$emit('save', {
            type: 'card',
            token: token.id,
            save_for_future: this.saveCard,
            billing_details: {
              name: this.cardholderName,
              email: this.email
            }
          })
        } else {
          if (!this.paypalEmail) {
            throw new Error('L\'email PayPal est requis')
          }
          await this.$emit('save', {
            type: 'paypal',
            email: this.paypalEmail,
            billing_details: {
              email: this.paypalEmail
            }
          })
        }
      } catch (error) {
        const errorMessage = error.message || 'Erreur lors de l\'enregistrement du moyen de paiement'
        this.$toast.error(errorMessage)
        const displayError = document.getElementById('card-errors')
        if (displayError) {
          displayError.textContent = errorMessage
        }
      }
    },

    getPaymentIcon(type) {
      return {
        'card': 'fas fa-credit-card',
        'paypal': 'fab fa-paypal'
      }[type] || 'fas fa-money-bill'
    },

    formatExpiry(date) {
      return new Date(date).toLocaleDateString('fr-FR', {
        month: 'numeric',
        year: 'numeric'
      })
    }
  },

  beforeDestroy() {
    if (this.card) {
      this.card.destroy()
    }
  }
}
</script>

<style scoped>
.payment-method-form {
  padding: 1rem;
}

.payment-type-selector {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
}

.type-btn {
  flex: 1;
  padding: 1rem;
  border: 2px solid #e5e7eb;
  border-radius: 0.5rem;
  background: white;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  cursor: pointer;
  transition: all 0.2s;
}

.type-btn.active {
  border-color: #3B82F6;
  background: #EBF5FF;
  color: #3B82F6;
}

.card-element {
  padding: 1rem;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  background: white;
}

.error-message {
  color: #EF4444;
  margin-top: 0.5rem;
  font-size: 0.875rem;
}

.save-card {
  margin-top: 1rem;
}

.checkbox-container {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
}

.form-group {
  margin-bottom: 1rem;
}

label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #374151;
}

.form-control {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #D1D5DB;
  border-radius: 0.375rem;
  background-color: white;
}

.paypal-button {
  margin-top: 1rem;
}

.current-method {
  margin-top: 2rem;
  padding-top: 1rem;
  border-top: 1px solid #e5e7eb;
}

.method-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-top: 0.5rem;
  padding: 1rem;
  background: #f9fafb;
  border-radius: 0.5rem;
}

.expires {
  color: #6B7280;
  font-size: 0.875rem;
  margin-left: 0.5rem;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 2rem;
}

.btn-cancel {
  padding: 0.5rem 1rem;
  background: #9CA3AF;
  color: white;
  border: none;
  border-radius: 0.375rem;
  cursor: pointer;
}

.btn-save {
  padding: 0.5rem 1rem;
  background: #3B82F6;
  color: white;
  border: none;
  border-radius: 0.375rem;
  cursor: pointer;
}

.btn-save:disabled {
  background: #9CA3AF;
  cursor: not-allowed;
}
</style>