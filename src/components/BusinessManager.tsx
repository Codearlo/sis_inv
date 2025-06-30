import React, { useState } from 'react'
import { useBusiness } from '../contexts/BusinessContext'
import { Plus, Building2, Edit3, Trash2, Check, X } from 'lucide-react'

export default function BusinessManager() {
  const { 
    businesses, 
    currentBusiness, 
    switchBusiness, 
    createBusiness, 
    updateBusiness, 
    deleteBusiness, 
    canCreateBusiness 
  } = useBusiness()

  const [showCreateForm, setShowCreateForm] = useState(false)
  const [editingId, setEditingId] = useState<string | null>(null)
  const [formData, setFormData] = useState({
    name: '',
    description: ''
  })
  const [error, setError] = useState('')

  const handleCreateSubmit = (e: React.FormEvent) => {
    e.preventDefault()
    setError('')
    
    try {
      createBusiness(formData.name, formData.description)
      setFormData({ name: '', description: '' })
      setShowCreateForm(false)
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Error al crear el negocio')
    }
  }

  const handleEditSubmit = (businessId: string) => {
    setError('')
    
    try {
      updateBusiness(businessId, {
        name: formData.name,
        description: formData.description
      })
      setEditingId(null)
      setFormData({ name: '', description: '' })
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Error al actualizar el negocio')
    }
  }

  const startEdit = (business: any) => {
    setEditingId(business.id)
    setFormData({
      name: business.name,
      description: business.description || ''
    })
  }

  const cancelEdit = () => {
    setEditingId(null)
    setFormData({ name: '', description: '' })
  }

  const handleDelete = (businessId: string) => {
    if (window.confirm('¿Estás seguro de que quieres eliminar este negocio? Se perderán todos los datos asociados.')) {
      try {
        deleteBusiness(businessId)
      } catch (err) {
        setError(err instanceof Error ? err.message : 'Error al eliminar el negocio')
      }
    }
  }

  return (
    <div className="space-y-8">
      {/* Header */}
      <div className="flex justify-between items-center">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Gestión de Negocios</h1>
          <p className="text-gray-600 mt-1">
            Administra hasta 2 negocios independientes con datos separados
          </p>
        </div>
        {canCreateBusiness && (
          <button
            onClick={() => setShowCreateForm(true)}
            className="inline-flex items-center px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition-colors font-medium"
          >
            <Plus className="h-4 w-4 mr-2" />
            Nuevo Negocio
          </button>
        )}
      </div>

      {error && (
        <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
          {error}
        </div>
      )}

      {/* Create Form */}
      {showCreateForm && (
        <div className="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
          <h3 className="text-lg font-semibold text-gray-900 mb-4">Crear Nuevo Negocio</h3>
          <form onSubmit={handleCreateSubmit} className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Nombre del Negocio
              </label>
              <input
                type="text"
                value={formData.name}
                onChange={(e) => setFormData(prev => ({ ...prev, name: e.target.value }))}
                placeholder="Ej: Tienda de Electrónicos"
                required
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent"
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Descripción (opcional)
              </label>
              <textarea
                value={formData.description}
                onChange={(e) => setFormData(prev => ({ ...prev, description: e.target.value }))}
                placeholder="Descripción del negocio..."
                rows={3}
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent"
              />
            </div>
            <div className="flex gap-3">
              <button
                type="submit"
                className="px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition-colors"
              >
                Crear Negocio
              </button>
              <button
                type="button"
                onClick={() => {
                  setShowCreateForm(false)
                  setFormData({ name: '', description: '' })
                }}
                className="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
              >
                Cancelar
              </button>
            </div>
          </form>
        </div>
      )}

      {/* Business List */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        {businesses.map((business) => (
          <div
            key={business.id}
            className={`bg-white rounded-xl border-2 shadow-sm p-6 transition-all ${
              currentBusiness?.id === business.id
                ? 'border-gray-900 ring-2 ring-gray-900 ring-opacity-20'
                : 'border-gray-200 hover:border-gray-300'
            }`}
          >
            {editingId === business.id ? (
              <div className="space-y-4">
                <input
                  type="text"
                  value={formData.name}
                  onChange={(e) => setFormData(prev => ({ ...prev, name: e.target.value }))}
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent font-semibold"
                />
                <textarea
                  value={formData.description}
                  onChange={(e) => setFormData(prev => ({ ...prev, description: e.target.value }))}
                  rows={2}
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                />
                <div className="flex gap-2">
                  <button
                    onClick={() => handleEditSubmit(business.id)}
                    className="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                  >
                    <Check className="h-4 w-4" />
                  </button>
                  <button
                    onClick={cancelEdit}
                    className="p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors"
                  >
                    <X className="h-4 w-4" />
                  </button>
                </div>
              </div>
            ) : (
              <>
                <div className="flex items-start justify-between mb-4">
                  <div className="flex items-center">
                    <div className="p-2 bg-gray-100 rounded-lg mr-3">
                      <Building2 className="h-6 w-6 text-gray-600" />
                    </div>
                    <div>
                      <h3 className="text-lg font-semibold text-gray-900">
                        {business.name}
                      </h3>
                      {currentBusiness?.id === business.id && (
                        <span className="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                          Activo
                        </span>
                      )}
                    </div>
                  </div>
                  <div className="flex gap-1">
                    <button
                      onClick={() => startEdit(business)}
                      className="p-2 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors"
                    >
                      <Edit3 className="h-4 w-4" />
                    </button>
                    {businesses.length > 1 && (
                      <button
                        onClick={() => handleDelete(business.id)}
                        className="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                      >
                        <Trash2 className="h-4 w-4" />
                      </button>
                    )}
                  </div>
                </div>

                {business.description && (
                  <p className="text-gray-600 text-sm mb-4">{business.description}</p>
                )}

                <div className="text-xs text-gray-500 mb-4">
                  Creado: {new Date(business.createdAt).toLocaleDateString()}
                </div>

                {currentBusiness?.id !== business.id && (
                  <button
                    onClick={() => switchBusiness(business.id)}
                    className="w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium"
                  >
                    Cambiar a este negocio
                  </button>
                )}
              </>
            )}
          </div>
        ))}
      </div>

      {/* Info Card */}
      <div className="bg-blue-50 border border-blue-200 rounded-xl p-6">
        <h3 className="text-lg font-semibold text-blue-900 mb-2">
          Información sobre Negocios Múltiples
        </h3>
        <ul className="text-blue-800 text-sm space-y-1">
          <li>• Puedes tener hasta 2 negocios por cuenta</li>
          <li>• Cada negocio mantiene sus datos completamente separados</li>
          <li>• Los productos, pedidos y cuentas son independientes entre negocios</li>
          <li>• Puedes cambiar entre negocios en cualquier momento</li>
          <li>• Al eliminar un negocio, se borran todos sus datos permanentemente</li>
        </ul>
      </div>
    </div>
  )
}