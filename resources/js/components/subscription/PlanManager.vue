<template>
  <div class="plan-manager">
    <div class="header">
      <h2 class="title">Gestion des Plans</h2>
      <button class="btn-primary" @click="showCreateModal = true">
        <i class="fas fa-plus"></i> Nouveau Plan
      </button>
    </div>

    <!-- Liste des plans -->
    <div class="plans-grid">
      <div v-for="plan in plans" :key="plan.id" class="plan-card">
        <div class="plan-header">
          <h3>{{ plan.name }}</h3>
          <span :class="['status-badge', plan.is_active ? 'active' : 'inactive']">
            {{ plan.is_active ? 'Actif' : 'Inactif' }}
          </span>
        </div>
        
        <div class="plan-price">
          {{ formatPrice(plan.price) }} / {{ plan.billing_cycle }}
        </div>
        
        <div class="plan-features">
          <ul>
            <li v-for="feature in plan.features" :key="feature">
              <i class="fas fa-check"></i> {{ feature }}
            </li>
          </ul>
        </div>
        
        <div class="plan-limits">
          <div class="limit-item">
            <span class="limit-label">Licences max:</span>
            <span class="limit-value">{{ plan.max_licenses }}</span>
          </div>
          <div class="limit-item">
            <span class="limit-label">Projets max:</span>
            <span class="limit-value">{{ plan.max_projects }}</span>
          </div>
          <div class="limit-item">
            <span class="limit-label">Clients max:</span>
            <span class="limit-value">{{ plan.max_clients }}</span>
          </div>
        </div>
        
        <div class="plan-actions">
          <button class="btn-edit" @click="editPlan(plan)">
            <i class="fas fa-edit"></i> Modifier
          </button>
          <button class="btn-delete" @click="confirmDelete(plan)">
            <i class="fas fa-trash"></i> Supprimer
          </button>
        </div>
      </div>
    </div>

    <!-- Modal de création/édition -->
    <modal v-model="showCreateModal" :title="isEditing ? 'Modifier le plan' : 'Nouveau plan'">
      <plan-form
        :plan="currentPlan"
        @save="savePlan"
        @cancel="closeModal"
      />
    </modal>

    <!-- Modal de confirmation de suppression -->
    <confirm-modal
      v-model="showDeleteModal"
      title="Confirmer la suppression"
      :message="`Êtes-vous sûr de vouloir supprimer le plan '${planToDelete?.name}' ?`"
      @confirm="deletePlan"
    />
  </div>
</template>

<script>
export default {
  name: 'PlanManager',
  
  data() {
    return {
      plans: [],
      showCreateModal: false,
      showDeleteModal: false,
      currentPlan: null,
      planToDelete: null,
      isEditing: false
    }
  },

  created() {
    this.loadPlans()
  },

  methods: {
    async loadPlans() {
      try {
        const response = await this.$axios.get('/api/plans')
        this.plans = response.data.plans
      } catch (error) {
        this.$toast.error('Erreur lors du chargement des plans')
      }
    },

    formatPrice(price) {
      return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
      }).format(price)
    },

    editPlan(plan) {
      this.currentPlan = { ...plan }
      this.isEditing = true
      this.showCreateModal = true
    },

    async savePlan(planData) {
      try {
        if (this.isEditing) {
          await this.$axios.put(`/api/plans/${planData.id}`, planData)
          this.$toast.success('Plan mis à jour avec succès')
        } else {
          await this.$axios.post('/api/plans', planData)
          this.$toast.success('Plan créé avec succès')
        }
        this.loadPlans()
        this.closeModal()
      } catch (error) {
        this.$toast.error('Erreur lors de la sauvegarde du plan')
      }
    },

    confirmDelete(plan) {
      this.planToDelete = plan
      this.showDeleteModal = true
    },

    async deletePlan() {
      try {
        await this.$axios.delete(`/api/plans/${this.planToDelete.id}`)
        this.$toast.success('Plan supprimé avec succès')
        this.loadPlans()
        this.showDeleteModal = false
        this.planToDelete = null
      } catch (error) {
        this.$toast.error('Erreur lors de la suppression du plan')
      }
    },

    closeModal() {
      this.showCreateModal = false
      this.currentPlan = null
      this.isEditing = false
    }
  }
}
</script>

<style scoped>
.plan-manager {
  padding: 2rem;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #2c3e50;
}

.plans-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 2rem;
}

.plan-card {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  padding: 1.5rem;
}

.plan-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 999px;
  font-size: 0.875rem;
}

.status-badge.active {
  background: #10B981;
  color: white;
}

.status-badge.inactive {
  background: #EF4444;
  color: white;
}

.plan-price {
  font-size: 1.5rem;
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 1rem;
}

.plan-features {
  margin-bottom: 1rem;
}

.plan-features ul {
  list-style: none;
  padding: 0;
}

.plan-features li {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.5rem;
}

.plan-limits {
  border-top: 1px solid #e5e7eb;
  padding-top: 1rem;
  margin-bottom: 1rem;
}

.limit-item {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.5rem;
}

.plan-actions {
  display: flex;
  gap: 1rem;
}

.btn-primary {
  background: #3B82F6;
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.btn-edit {
  background: #10B981;
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  flex: 1;
}

.btn-delete {
  background: #EF4444;
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  flex: 1;
}
</style>