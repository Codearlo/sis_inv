import React, { createContext, useContext, useState, useEffect } from 'react'

export interface Business {
  id: string
  name: string
  description?: string
  createdAt: string
  isActive: boolean
}

interface BusinessContextType {
  businesses: Business[]
  currentBusiness: Business | null
  switchBusiness: (businessId: string) => void
  createBusiness: (name: string, description?: string) => void
  updateBusiness: (businessId: string, updates: Partial<Business>) => void
  deleteBusiness: (businessId: string) => void
  canCreateBusiness: boolean
}

const BusinessContext = createContext<BusinessContextType | undefined>(undefined)

export function BusinessProvider({ children }: { children: React.ReactNode }) {
  const [businesses, setBusinesses] = useState<Business[]>([])
  const [currentBusiness, setCurrentBusiness] = useState<Business | null>(null)

  useEffect(() => {
    // Load businesses from localStorage
    const savedBusinesses = localStorage.getItem('businesses')
    const savedCurrentBusinessId = localStorage.getItem('currentBusinessId')
    
    if (savedBusinesses) {
      const parsedBusinesses = JSON.parse(savedBusinesses)
      setBusinesses(parsedBusinesses)
      
      if (savedCurrentBusinessId) {
        const current = parsedBusinesses.find((b: Business) => b.id === savedCurrentBusinessId)
        if (current) {
          setCurrentBusiness(current)
        } else if (parsedBusinesses.length > 0) {
          setCurrentBusiness(parsedBusinesses[0])
          localStorage.setItem('currentBusinessId', parsedBusinesses[0].id)
        }
      } else if (parsedBusinesses.length > 0) {
        setCurrentBusiness(parsedBusinesses[0])
        localStorage.setItem('currentBusinessId', parsedBusinesses[0].id)
      }
    } else {
      // Create default business
      const defaultBusiness: Business = {
        id: '1',
        name: 'Mi Negocio Principal',
        description: 'Negocio principal de inventario',
        createdAt: new Date().toISOString(),
        isActive: true
      }
      setBusinesses([defaultBusiness])
      setCurrentBusiness(defaultBusiness)
      localStorage.setItem('businesses', JSON.stringify([defaultBusiness]))
      localStorage.setItem('currentBusinessId', defaultBusiness.id)
    }
  }, [])

  const switchBusiness = (businessId: string) => {
    const business = businesses.find(b => b.id === businessId)
    if (business) {
      setCurrentBusiness(business)
      localStorage.setItem('currentBusinessId', businessId)
    }
  }

  const createBusiness = (name: string, description?: string) => {
    if (businesses.length >= 2) {
      throw new Error('Solo puedes tener máximo 2 negocios')
    }

    const newBusiness: Business = {
      id: Date.now().toString(),
      name,
      description,
      createdAt: new Date().toISOString(),
      isActive: true
    }

    const updatedBusinesses = [...businesses, newBusiness]
    setBusinesses(updatedBusinesses)
    localStorage.setItem('businesses', JSON.stringify(updatedBusinesses))
    
    // Switch to new business
    setCurrentBusiness(newBusiness)
    localStorage.setItem('currentBusinessId', newBusiness.id)
  }

  const updateBusiness = (businessId: string, updates: Partial<Business>) => {
    const updatedBusinesses = businesses.map(b => 
      b.id === businessId ? { ...b, ...updates } : b
    )
    setBusinesses(updatedBusinesses)
    localStorage.setItem('businesses', JSON.stringify(updatedBusinesses))
    
    if (currentBusiness?.id === businessId) {
      setCurrentBusiness({ ...currentBusiness, ...updates })
    }
  }

  const deleteBusiness = (businessId: string) => {
    if (businesses.length <= 1) {
      throw new Error('Debes tener al menos un negocio')
    }

    const updatedBusinesses = businesses.filter(b => b.id !== businessId)
    setBusinesses(updatedBusinesses)
    localStorage.setItem('businesses', JSON.stringify(updatedBusinesses))
    
    // Clear business-specific data
    localStorage.removeItem(`products_${businessId}`)
    localStorage.removeItem(`orders_${businessId}`)
    
    if (currentBusiness?.id === businessId) {
      setCurrentBusiness(updatedBusinesses[0])
      localStorage.setItem('currentBusinessId', updatedBusinesses[0].id)
    }
  }

  const canCreateBusiness = businesses.length < 2

  return (
    <BusinessContext.Provider value={{
      businesses,
      currentBusiness,
      switchBusiness,
      createBusiness,
      updateBusiness,
      deleteBusiness,
      canCreateBusiness
    }}>
      {children}
    </BusinessContext.Provider>
  )
}

export function useBusiness() {
  const context = useContext(BusinessContext)
  if (context === undefined) {
    throw new Error('useBusiness must be used within a BusinessProvider')
  }
  return context
}