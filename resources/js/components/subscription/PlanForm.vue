<template>
  <form @submit.prevent="handleSubmit" class="plan-form">
    <div class="form-group">
      <label for="name">Nom du plan</label>
      <input
        id="name"
        v-model="formData.name"
        type="text"
        required
        class="form-control"
        placeholder="Ex: Plan Pro"
      >
    </div>

    <div class="form-group">
      <label for="slug">Slug</label>
      <input
        id="slug"
        v-model="formData.slug"
        type="text"
        required
        class="form-control"
        placeholder="Ex: plan-pro"
      >
    </div>

    <div class="form-group">
      <label for="description">Description</label>
      <textarea
        id="description"
        v-model="formData.description"
        class="form-control"
        rows="3"
        placeholder="Description du plan..."
      ></textarea>
    </div>

    <div class="form-row">
      <div class="form-group col">
        <label for="price">Prix</label>
        <input
          id="price"
          v-model.number="formData.price"
          type="number"
          step="0.01"
          required
          class="form-control"
          placeholder="0.00"
        >
      </div>

      <div class="form-group col">
        <label for="billing_cycle">Cycle de facturation</label>
        <select
          id="billing_cycle"
          v-model="formData.billing_cycle"
          required
          class="form-control"
        >
          <option value="monthly">Mensuel</option>
          <option value="yearly">Annuel</option>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label>Fonctionnalités</label>
      <div class="features-list">
        <div v-for="(feature, index) in formData.features" :key="index" class="feature-item">
          <input
            v-model="formData.features[index]"
            type="text"
            class="form-control"
            placeholder="Nouvelle fonctionnalité"
          >
          <button type="button" class="btn-remove" @click="removeFeature(index)">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <button type="button" class="btn-add" @click="addFeature">
          <i class="fas fa-plus"></i> Ajouter une fonctionnalité
        </button>
      </div>
    </div>

    <div class="form-row">
      <div class="form-group col">
        <label for="max_licenses">Licences maximum</label>
        <input
          id="max_licenses"
          v-model.number="formData.max_licenses"
          type="number"
          required
          class="form-control"
          min="1"
        >
      </div>

      <div class="form-group col">
        <label for="max_projects">Projets maximum</label>
        <input
          id="max_projects"
          v-model.number="formData.max_projects"
          type="number"
          required
          class="form-control"
          min="1"
        >
      </div>

      <div class="form-group col">
        <label for="max_clients">Clients maximum</label>
        <input
          id="max_clients"
          v-model.number="formData.max_clients"
          type="number"
          required
          class="form-control"
          min="1"
        >
      </div>
    </div>

    <div class="form-row">
      <div class="form-group col">
        <label for="trial_days">Jours d'essai</label>
        <input
          id="trial_days"
          v-model.number="formData.trial_days"
          type="number"
          class="form-control"
          min="0"
        >
      </div>

      <div class="form-group col">
        <label for="stripe_price_id">ID Prix Stripe</label>
        <input
          id="stripe_price_id"
          v-model="formData.stripe_price_id"
          type="text"
          class="form-control"
          placeholder="price_..."
        >
      </div>

      <div class="form-group col">
        <label for="paypal_plan_id">ID Plan PayPal</label>
        <input
          id="paypal_plan_id"
          v-model="formData.paypal_plan_id"
          type="text"
          class="form-control"
          placeholder="P-..."
        >
      </div>
    </div>

    <div class="form-group">
      <label class="checkbox-container">
        <input
          type="checkbox"
          v-model="formData.is_active"
        >
        <span class="checkbox-label">Plan actif</span>
      </label>
    </div>

    <div class="form-actions">
      <button type="button" class="btn-cancel" @click="$emit('cancel')">
        Annuler
      </button>
      <button type="submit" class="btn-save">
        {{ isEditing ? 'Mettre à jour' : 'Créer' }}
      </button>
    </div>
  </form>
</template>

<script>
export default {
  name: 'PlanForm',

  props: {
    plan: {
      type: Object,
      default: null
    }
  },

  data() {
    return {
      formData: {
        name: '',
        slug: '',
        description: '',
        price: 0,
        billing_cycle: 'monthly',
        features: [],
        is_active: true,
        stripe_price_id: '',
        paypal_plan_id: '',
        trial_days: 0,
        max_licenses: 1,
        max_projects: 1,
        max_clients: 1
      }
    }
  },

  computed: {
    isEditing() {
      return !!this.plan
    }
  },

  created() {
    if (this.plan) {
      this.formData = { ...this.plan }
    }
  },

  methods: {
    handleSubmit() {
      this.$emit('save', this.formData)
    },

    addFeature() {
      this.formData.features.push('')
    },

    removeFeature(index) {
      this.formData.features.splice(index, 1)
    }
  }
}
</script>

<style scoped>
.plan-form {
  padding: 1rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
  margin-bottom: 1.5rem;
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

.form-control:focus {
  border-color: #3B82F6;
  outline: none;
  box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
}

.features-list {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.feature-item {
  display: flex;
  gap: 0.5rem;
}

.btn-remove {
  padding: 0.5rem;
  background: #EF4444;
  color: white;
  border: none;
  border-radius: 0.375rem;
  cursor: pointer;
}

.btn-add {
  padding: 0.5rem;
  background: #10B981;
  color: white;
  border: none;
  border-radius: 0.375rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-top: 0.5rem;
}

.checkbox-container {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
}

.checkbox-label {
  margin-bottom: 0;
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
</style>