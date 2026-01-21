<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="isOpen" class="modal-overlay" @click="handleBackdropClick">
        <div class="modal-container" @click.stop>
          <div class="modal-header">
            <div class="flex items-center gap-3">
              <div
                :class="[
                  'w-12 h-12 rounded-full flex items-center justify-center',
                  variantClasses[variant].bg,
                ]"
              >
                <i :class="[variantClasses[variant].icon, 'text-2xl']"></i>
              </div>
              <div>
                <h2 class="modal-title">{{ title }}</h2>
                <p v-if="subtitle" class="text-sm text-gray-500 mt-0.5">{{ subtitle }}</p>
              </div>
            </div>
            <button class="modal-close" @click="close" aria-label="Fermer">
              <i class="fa-solid fa-xmark"></i>
            </button>
          </div>
          <div class="modal-body">
            <slot></slot>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
              :disabled="isLoading"
              @click="cancel"
            >
              {{ cancelText }}
            </button>
            <button
              type="button"
              :class="[
                'px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2',
                variantClasses[variant].button,
              ]"
              :disabled="isLoading"
              @click="confirm"
            >
              <i v-if="isLoading" class="fas fa-spinner fa-spin"></i>
              <i v-else :class="variantClasses[variant].buttonIcon"></i>
              {{ isLoading ? loadingText : confirmText }}
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
type ModalVariant = 'danger' | 'warning' | 'info' | 'success'

interface Props {
  isOpen: boolean
  title: string
  subtitle?: string
  variant?: ModalVariant
  confirmText?: string
  cancelText?: string
  loadingText?: string
  isLoading?: boolean
  closeOnBackdrop?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'warning',
  confirmText: 'Confirmer',
  cancelText: 'Annuler',
  loadingText: 'Chargement...',
  isLoading: false,
  closeOnBackdrop: true,
})

const emit = defineEmits<{
  close: []
  confirm: []
  cancel: []
}>()

const variantClasses: Record<
  ModalVariant,
  { bg: string; icon: string; button: string; buttonIcon: string }
> = {
  danger: {
    bg: 'bg-red-100',
    icon: 'fas fa-exclamation-triangle text-red-600',
    button: 'bg-red-600 hover:bg-red-700',
    buttonIcon: 'fas fa-trash',
  },
  warning: {
    bg: 'bg-yellow-100',
    icon: 'fas fa-exclamation-circle text-yellow-600',
    button: 'bg-yellow-600 hover:bg-yellow-700',
    buttonIcon: 'fas fa-exclamation',
  },
  info: {
    bg: 'bg-blue-100',
    icon: 'fas fa-info-circle text-blue-600',
    button: 'bg-blue-600 hover:bg-blue-700',
    buttonIcon: 'fas fa-check',
  },
  success: {
    bg: 'bg-green-100',
    icon: 'fas fa-check-circle text-green-600',
    button: 'bg-green-600 hover:bg-green-700',
    buttonIcon: 'fas fa-check',
  },
}

const close = () => {
  if (!props.isLoading) {
    emit('close')
  }
}

const confirm = () => {
  if (!props.isLoading) {
    emit('confirm')
  }
}

const cancel = () => {
  if (!props.isLoading) {
    emit('cancel')
    emit('close')
  }
}

const handleBackdropClick = () => {
  if (props.closeOnBackdrop && !props.isLoading) {
    close()
  }
}
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-container {
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  max-width: 90%;
  max-height: 90vh;
  width: 500px;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: start;
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.modal-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: #111827;
  margin: 0;
}

.modal-close {
  background: none;
  border: none;
  cursor: pointer;
  padding: 0.5rem;
  color: #6b7280;
  font-size: 1.25rem;
  line-height: 1;
  transition: color 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal-close:hover {
  color: #111827;
}

.modal-body {
  padding: 1.5rem;
  overflow-y: auto;
  flex: 1;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  padding: 1.5rem;
  border-top: 1px solid #e5e7eb;
}

.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-active .modal-container,
.modal-leave-active .modal-container {
  transition: transform 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-from .modal-container,
.modal-leave-to .modal-container {
  transform: scale(0.9);
}
</style>
