import React, { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { useData } from '../contexts/DataContext'

export default function NuevoPedido() {
  const navigate = useNavigate()
  const { accounts, addOrder } = useData()
  
  const [formData, setFormData] = useState({
    productName: '',
    supplier: '',
    purchaseDate: '',
    receivedDate: '',
    unitPrice: '',
    quantity: '',
    paymentStatus: 'pagado' as 'pagado' | 'deuda',
    accountId: ''
  })
  
  const [message, setMessage] = useState('')
  const [messageType, setMessageType] = useState<'success' | 'error'>('success')

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault()
    
    try {
      addOrder({
        productId: '', // Will be set in addOrder function
        productName: formData.productName,
        supplier: formData.supplier,
        purchaseDate: formData.purchaseDate,
        receivedDate: formData.receivedDate || undefined,
        unitPrice: parseFloat(formData.unitPrice),
        quantity: parseInt(formData.quantity),
        paymentStatus: formData.paymentStatus,
        accountId: formData.paymentStatus === 'deuda' ? formData.accountId : undefined
      })
      
      setMessage('Pedido registrado con éxito.')
      setMessageType('success')
      
      // Reset form
      setFormData({
        productName: '',
        supplier: '',
        purchaseDate: '',
        receivedDate: '',
        unitPrice: '',
        quantity: '',
        paymentStatus: 'pagado',
        accountId: ''
      })
      
      // Redirect after 2 seconds
      setTimeout(() => {
        navigate('/pedidos')
      }, 2000)
      
    } catch (error) {
      setMessage('Error al registrar el pedido.')
      setMessageType('error')
    }
  }

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
    const { name, value } = e.target
    setFormData(prev => ({
      ...prev,
      [name]: value
    }))
  }

  return (
    <div className="max-w-2xl">
      <header className="mb-8">
        <h1 className="text-2xl font-bold text-gray-900">Registrar Nuevo Pedido</h1>
      </header>

      <div className="bg-white rounded-xl border border-gray-200 shadow-sm p-8">
        {message && (
          <div className={`mb-6 p-4 rounded-lg ${
            messageType === 'success' 
              ? 'bg-green-50 text-green-800 border border-green-200' 
              : 'bg-red-50 text-red-800 border border-red-200'
          }`}>
            {message}
            {messageType === 'success' && (
              <span className="block mt-1">
                <a href="/pedidos" className="font-semibold underline">Volver al listado</a>
              </span>
            )}
          </div>
        )}

        <form onSubmit={handleSubmit} className="space-y-6">
          <div>
            <label htmlFor="productName" className="block text-sm font-medium text-gray-700 mb-2">
              Nombre del producto
            </label>
            <input
              type="text"
              id="productName"
              name="productName"
              value={formData.productName}
              onChange={handleChange}
              placeholder="Ej: Teclado Mecánico RGB"
              required
              className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent"
            />
          </div>

          <div>
            <label htmlFor="supplier" className="block text-sm font-medium text-gray-700 mb-2">
              Proveedor
            </label>
            <input
              type="text"
              id="supplier"
              name="supplier"
              value={formData.supplier}
              onChange={handleChange}
              placeholder="Ej: AliExpress, Amazon, etc."
              required
              className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent"
            />
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label htmlFor="purchaseDate" className="block text-sm font-medium text-gray-700 mb-2">
                Fecha de compra
              </label>
              <input
                type="date"
                id="purchaseDate"
                name="purchaseDate"
                value={formData.purchaseDate}
                onChange={handleChange}
                required
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent"
              />
            </div>

            <div>
              <label htmlFor="receivedDate" className="block text-sm font-medium text-gray-700 mb-2">
                Fecha de recepción (opcional)
              </label>
              <input
                type="date"
                id="receivedDate"
                name="receivedDate"
                value={formData.receivedDate}
                onChange={handleChange}
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent"
              />
            </div>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label htmlFor="unitPrice" className="block text-sm font-medium text-gray-700 mb-2">
                Precio por unidad
              </label>
              <input
                type="number"
                id="unitPrice"
                name="unitPrice"
                value={formData.unitPrice}
                onChange={handleChange}
                step="0.01"
                placeholder="Ej: 45.50"
                required
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent"
              />
            </div>

            <div>
              <label htmlFor="quantity" className="block text-sm font-medium text-gray-700 mb-2">
                Cantidad
              </label>
              <input
                type="number"
                id="quantity"
                name="quantity"
                value={formData.quantity}
                onChange={handleChange}
                placeholder="Ej: 10"
                required
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent"
              />
            </div>
          </div>

          <div>
            <label className="block text-sm font-medium text-gray-700 mb-3">
              Estado del Pago
            </label>
            <div className="flex gap-6">
              <label className="flex items-center">
                <input
                  type="radio"
                  name="paymentStatus"
                  value="pagado"
                  checked={formData.paymentStatus === 'pagado'}
                  onChange={handleChange}
                  className="h-4 w-4 text-gray-900 focus:ring-gray-900 border-gray-300"
                />
                <span className="ml-2 text-sm text-gray-700">Pagado Completo</span>
              </label>
              <label className="flex items-center">
                <input
                  type="radio"
                  name="paymentStatus"
                  value="deuda"
                  checked={formData.paymentStatus === 'deuda'}
                  onChange={handleChange}
                  className="h-4 w-4 text-gray-900 focus:ring-gray-900 border-gray-300"
                />
                <span className="ml-2 text-sm text-gray-700">Deuda</span>
              </label>
            </div>
          </div>

          {formData.paymentStatus === 'deuda' && (
            <div>
              <label htmlFor="accountId" className="block text-sm font-medium text-gray-700 mb-2">
                Cuenta financiera asociada
              </label>
              <select
                id="accountId"
                name="accountId"
                value={formData.accountId}
                onChange={handleChange}
                required={formData.paymentStatus === 'deuda'}
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent"
              >
                <option value="">-- Seleccione una cuenta --</option>
                {accounts.map((account) => (
                  <option key={account.id} value={account.id}>
                    {account.name}
                  </option>
                ))}
              </select>
            </div>
          )}

          <button
            type="submit"
            className="w-full bg-gray-900 text-white py-3 px-4 rounded-lg hover:bg-gray-800 transition-colors font-medium"
          >
            Registrar Pedido
          </button>
        </form>
      </div>
    </div>
  )
}