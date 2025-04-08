<template>
  <div class="subscription-manager">
    <div class="header">
      <h2 class="title">Gestion de l'Abonnement</h2>
    </div>

    <!-- Informations sur l'abonnement actuel -->
    <div v-if="currentSubscription" class="current-subscription">
      <div class="subscription-header">
        <h3>Abonnement Actuel</h3>
        <span :class="['status-badge', getStatusClass(currentSubscription.status)]">
          {{ getStatusLabel(currentSubscription.status) }}
        </span>
      </div>

      <div class="subscription-details">
        <div class="plan-info">
          <h4>{{ currentSubscription.plan.name }}</h4>
          <p class="price">{{ formatPrice(currentSubscription.renewal_price) }} / {{ currentSubscription.billing_cycle }}</p>
        </div>

        <div class="subscription-dates">
          <div class="date-item">
            <span class="label">Début:</span>
            <span class="value">{{ formatDate(currentSubscription.starts_at) }}</span>
          </div>
          <div class="date-item">
            <span class="label">Fin:</span>
            <span class="value">{{ formatDate(currentSubscription.ends_at) }}</span>
          </div>
          <div v-if="currentSubscription.trial_ends_at" class="date-item">
            <span class="label">Fin de l'essai:</span>
            <span class="value">{{ formatDate(currentSubscription.trial_ends_at) }}</span>
          </div>
        </div>

        <div class="subscription-usage">
          <div class="usage-item">
            <span class="label">Licences utilisées:</span>
            <span class="value">{{ usageStats.licenses_used }} / {{ currentSubscription.plan.max_licenses }}</span>
          </div>
          <div class="usage-item">
            <span class="label">Projets utilisés:</span>
            <span class="value">{{ usageStats.projects_used }} / {{ currentSubscription.plan.max_projects }}</span>
          </div>
          <div class="usage-item">
            <span class="label">Clients utilisés:</span>
            <span class="value">{{ usageStats.clients_used }} / {{ currentSubscription.plan.max_clients }}</span>
          </div>
        </div>

        <div class="payment-info">
          <h4>Méthode de paiement</h4>
          <div v-if="currentSubscription.payment_method" class="payment-method">
            <i :class="getPaymentIcon(currentSubscription.payment_method.type)"></i>
            <span v-if="currentSubscription.payment_method.type === 'card'">
              {{ currentSubscription.payment_method.card_brand }} **** {{ currentSubscription.payment_method.card_last_four }}
            </span>
            <span v-else-if="currentSubscription.payment_method.type === 'paypal'">
              PayPal ({{ currentSubscription.payment_method.paypal_email }})
            </span>
          </div>
        </div>
      </div>

      <div class="subscription-actions">
        <button v-if="canCancel" class="btn-cancel" @click="confirmCancellation">
          Annuler l'abonnement
        </button>
        <button v-if="canResume" class="btn-resume" @click="resumeSubscription">
          Reprendre l'abonnement
        </button>
        <button class="btn-change" @click="showChangePlan = true">
          Changer de plan
        </button>
        <button class="btn-payment" @click="showPaymentMethod = true">
          Gérer le paiement
        </button>
      </div>
    </div>

    <!-- Pas d'abonnement actif -->
    <div v-else class="no-subscription">
      <div class="message">
        <i class="fas fa-info-circle"></i>
        <p>Vous n'avez pas d'abonnement actif. Choisissez un plan pour commencer.</p>
      </div>
      <button class="btn-subscribe" @click="showChangePlan = true">
        Voir les plans
      </button>
    </div>

    <!-- Historique des factures -->
    <div class="invoices-section">
      <h3>Historique des factures</h3>
      <div class="invoices-list">
        <div v-for="invoice in invoices" :key="invoice.id" class="invoice-item">
          <div class="invoice-info">
            <span class="invoice-number">{{ invoice.number }}</span>
            <span class="invoice-date">{{ formatDate(invoice.created_at) }}</span>
          </div>
          <div class="invoice-amount">
            {{ formatPrice(invoice.total) }}
          </div>
          <div class="invoice-status">
            <span :class="['status-badge', getInvoiceStatusClass(invoice.status)]">
              {{ getInvoiceStatusLabel(invoice.status) }}
            </span>
          </div>
          <div class="invoice-actions">
            <button class="btn-download" @click="downloadInvoice(invoice.id)">
              <i class="fas fa-download"></i>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de changement de plan -->
    <modal v-model="showChangePlan" title="Changer de plan">
      <div class="plans-list">
        <div v-for="plan in availablePlans" :key="plan.id" class="plan-option">
          <div class="plan-details">
            <h4>{{ plan.name }}</h4>
            <p class="price">{{ formatPrice(plan.price) }} / {{ plan.billing_cycle }}</p>
            <ul class="features">
              <li v-for="feature in plan.features" :key="feature">
                <i class="fas fa-check"></i> {{ feature }}
              </li>
            </ul>
          </div>
          <button
            class="btn-select"
            :disabled="isCurrentPlan(plan)"
            @click="selectPlan(plan)"
          >
            {{ isCurrentPlan(plan) ? 'Plan actuel' : 'Sélectionner' }}
          </button>
        </div>
      </div>
    </modal>

    <!-- Modal de gestion du paiement -->
    <modal v-model="showPaymentMethod" title="Gérer le paiement">
      <payment-method-form
        :current-method="currentSubscription?.payment_method"
        @save="updatePaymentMethod"
      />
    </modal>

    <!-- Modal de confirmation d'annulation -->
    <confirm-modal
      v-model="showCancelModal"
      title="Confirmer l'annulation"
      message="Êtes-vous sûr de vouloir annuler votre abonnement ? Votre accès restera actif jusqu'à la fin de la période en cours."
      @confirm="cancelSubscription"
    />
  </div>
</template>

<script>
export default {
  name: 'SubscriptionManager',

  data() {
    return {
      currentSubscription: null,
      availablePlans: [],
      invoices: [],
      usageStats: {
        licenses_used: 0,
        projects_used: 0,
        clients_used: 0
      },
      showChangePlan: false,
      showPaymentMethod: false,
      showCancelModal: false
    }
  },

  computed: {
    canCancel() {
      return this.currentSubscription &&
             this.currentSubscription.status === 'active' &&
             !this.currentSubscription.canceled_at
    },

    canResume() {
      return this.currentSubscription &&
             this.currentSubscription.status === 'active' &&
             this.currentSubscription.canceled_at
    }
  },

  created() {
    this.loadSubscription()
    this.loadPlans()
    this.loadInvoices()
    this.loadUsageStats()
  },

  methods: {
    async loadSubscription() {
      try {
        const response = await this.$axios.get('/api/subscription')
        this.currentSubscription = response.data.subscription
      } catch (error) {
        this.$toast.error('Erreur lors du chargement de l\'abonnement')
      }
    },

    async loadPlans() {
      try {
        const response = await this.$axios.get('/api/plans')
        this.availablePlans = response.data.plans
      } catch (error) {
        this.$toast.error('Erreur lors du chargement des plans')
      }
    },

    async loadInvoices() {
      try {
        const response = await this.$axios.get('/api/invoices')
        this.invoices = response.data.invoices
      } catch (error) {
        this.$toast.error('Erreur lors du chargement des factures')
      }
    },

    async loadUsageStats() {
      try {
        const response = await this.$axios.get('/api/subscription/usage')
        this.usageStats = response.data
      } catch (error) {
        this.$toast.error('Erreur lors du chargement des statistiques')
      }
    },

    formatPrice(price) {
      return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
      }).format(price)
    },

    formatDate(date) {
      return new Date(date).toLocaleDateString('fr-FR')
    },

    getStatusClass(status) {
      return {
        'active': 'success',
        'canceled': 'warning',
        'expired': 'danger'
      }[status] || 'default'
    },

    getStatusLabel(status) {
      return {
        'active': 'Actif',
        'canceled': 'Annulé',
        'expired': 'Expiré'
      }[status] || status
    },

    getInvoiceStatusClass(status) {
      return {
        'paid': 'success',
        'pending': 'warning',
        'failed': 'danger'
      }[status] || 'default'
    },

    getInvoiceStatusLabel(status) {
      return {
        'paid': 'Payée',
        'pending': 'En attente',
        'failed': 'Échec'
      }[status] || status
    },

    getPaymentIcon(type) {
      return {
        'card': 'fas fa-credit-card',
        'paypal': 'fab fa-paypal'
      }[type] || 'fas fa-money-bill'
    },

    isCurrentPlan(plan) {
      return this.currentSubscription &&
             this.currentSubscription.plan.id === plan.id
    },

    async selectPlan(plan) {
      try {
        await this.$axios.post(`/api/subscription/change-plan/${plan.id}`)
        this.$toast.success('Plan mis à jour avec succès')
        this.loadSubscription()
        this.showChangePlan = false
      } catch (error) {
        this.$toast.error('Erreur lors du changement de plan')
      }
    },

    async updatePaymentMethod(paymentData) {
      try {
        await this.$axios.post('/api/subscription/payment-method', paymentData)
        this.$toast.success('Méthode de paiement mise à jour')
        this.loadSubscription()
        this.showPaymentMethod = false
      } catch (error) {
        this.$toast.error('Erreur lors de la mise à jour du paiement')
      }
    },

    confirmCancellation() {
      this.showCancelModal = true
    },

    async cancelSubscription() {
      try {
        await this.$axios.post('/api/subscription/cancel')
        this.$toast.success('Abonnement annulé avec succès')
        this.loadSubscription()
        this.showCancelModal = false
      } catch (error) {
        this.$toast.error('Erreur lors de l\'annulation')
      }
    },

    async resumeSubscription() {
      try {
        await this.$axios.post('/api/subscription/resume')
        this.$toast.success('Abonnement repris avec succès')
        this.loadSubscription()
      } catch (error) {
        this.$toast.error('Erreur lors de la reprise de l\'abonnement')
      }
    },

    async downloadInvoice(invoiceId) {
      try {
        const response = await this.$axios.get(`/api/invoices/${invoiceId}/download`, {
          responseType: 'blob'
        })
        const url = window.URL.createObjectURL(new Blob([response.data]))
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', `facture-${invoiceId}.pdf`)
        document.body.appendChild(link)
        link.click()
        link.remove()
      } catch (error) {
        this.$toast.error('Erreur lors du téléchargement de la facture')
      }
    }
  }
}
</script>

<style scoped>
.subscription-manager {
  padding: 2rem;
}

.header {
  margin-bottom: 2rem;
}

.title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #2c3e50;
}

.current-subscription {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  padding: 1.5rem;
  margin-bottom: 2rem;
}

.subscription-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.subscription-details {
  display: grid;
  gap: 1.5rem;
}

.plan-info {
  text-align: center;
}

.price {
  font-size: 1.25rem;
  font-weight: 600;
  color: #2c3e50;
}

.subscription-dates,
.subscription-usage {
  display: grid;
  gap: 1rem;
}

.date-item,
.usage-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.payment-info {
  border-top: 1px solid #e5e7eb;
  padding-top: 1rem;
}

.payment-method {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-top: 0.5rem;
}

.subscription-actions {
  display: flex;
  gap: 1rem;
  margin-top: 1.5rem;
  justify-content: flex-end;
}

.no-subscription {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  padding: 2rem;
  text-align: center;
  margin-bottom: 2rem;
}

.invoices-section {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  padding: 1.5rem;
}

.invoice-item {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr auto;
  align-items: center;
  padding: 1rem;
  border-bottom: 1px solid #e5e7eb;
}

.invoice-item:last-child {
  border-bottom: none;
}

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 999px;
  font-size: 0.875rem;
}

.status-badge.success {
  background: #10B981;
  color: white;
}

.status-badge.warning {
  background: #F59E0B;
  color: white;
}

.status-badge.danger {
  background: #EF4444;
  color: white;
}

.status-badge.default {
  background: #9CA3AF;
  color: white;
}

.btn-cancel {
  background: #EF4444;
  color: white;
}

.btn-resume {
  background: #10B981;
  color: white;
}

.btn-change,
.btn-payment,
.btn-subscribe {
  background: #3B82F6;
  color: white;
}

.btn-download {
  background: transparent;
  color: #3B82F6;
  padding: 0.5rem;
}

.btn-cancel,
.btn-resume,
.btn-change,
.btn-payment,
.btn-subscribe {
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  font-weight: 500;
  border: none;
  cursor: pointer;
}

.plans-list {
  display: grid;
  gap: 1rem;
  max-height: 60vh;
  overflow-y: auto;
}

.plan-option {
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 1rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.features {
  list-style: none;
  padding: 0;
  margin: 1rem 0;
}

.features li {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.5rem;
}

.btn-select {
  background: #3B82F6;
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  border: none;
  cursor: pointer;
}

.btn-select:disabled {
  background: #9CA3AF;
  cursor: not-allowed;
}
</style>