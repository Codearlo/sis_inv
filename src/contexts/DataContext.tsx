import React, { createContext, useContext, useState, useEffect } from 'react'
import { useBusiness } from './BusinessContext'

export interface Product {
  id: string
  name: string
  description?: string
  businessId: string
}

export interface Order {
  id: string
  productId: string
  productName: string
  supplier: string
  purchaseDate: string
  receivedDate?: string
  unitPrice: number
  quantity: number
  paymentStatus: 'pagado' | 'deuda'
  accountId?: string
  accountName?: string
  orderNumber: number
  businessId: string
}

export interface Account {
  id: string
  name: string
  type: 'tarjeta' | 'prestamo'
  bank?: string
  businessId: string
}

interface DataContextType {
  products: Product[]
  orders: Order[]
  accounts: Account[]
  addOrder: (order: Omit<Order, 'id' | 'orderNumber' | 'businessId'>) => void
  addAccount: (account: Omit<Account, 'id' | 'businessId'>) => void
  getDashboardData: () => {
    totalProducts: number
    totalStock: number
    lowStockProducts: Array<{ name: string; stock: number }>
    recentPurchases: Array<{ name: string; quantity: number; price: number; date: string }>
  }
}

const DataContext = createContext<DataContextType | undefined>(undefined)

export function DataProvider({ children }: { children: React.ReactNode }) {
  const { currentBusiness } = useBusiness()
  const [products, setProducts] = useState<Product[]>([])
  const [orders, setOrders] = useState<Order[]>([])
  const [accounts, setAccounts] = useState<Account[]>([])

  // Load data when business changes
  useEffect(() => {
    if (!currentBusiness) return

    const businessId = currentBusiness.id
    
    // Load products
    const savedProducts = localStorage.getItem(`products_${businessId}`)
    if (savedProducts) {
      setProducts(JSON.parse(savedProducts))
    } else {
      // Create sample products for new business
      const sampleProducts: Product[] = [
        { 
          id: '1', 
          name: 'Teclado Mecánico RGB', 
          description: 'Teclado gaming con switches azules',
          businessId 
        },
        { 
          id: '2', 
          name: 'Mouse Inalámbrico', 
          description: 'Mouse ergonómico con sensor óptico',
          businessId 
        },
        { 
          id: '3', 
          name: 'Monitor 24"', 
          description: 'Monitor Full HD IPS',
          businessId 
        }
      ]
      setProducts(sampleProducts)
      localStorage.setItem(`products_${businessId}`, JSON.stringify(sampleProducts))
    }

    // Load orders
    const savedOrders = localStorage.getItem(`orders_${businessId}`)
    if (savedOrders) {
      setOrders(JSON.parse(savedOrders))
    } else {
      // Create sample orders for new business
      const sampleOrders: Order[] = [
        {
          id: '1',
          productId: '1',
          productName: 'Teclado Mecánico RGB',
          supplier: 'AliExpress',
          purchaseDate: '2024-01-15',
          receivedDate: '2024-01-20',
          unitPrice: 45.50,
          quantity: 10,
          paymentStatus: 'pagado',
          orderNumber: 1,
          businessId
        },
        {
          id: '2',
          productId: '2',
          productName: 'Mouse Inalámbrico',
          supplier: 'Amazon',
          purchaseDate: '2024-01-16',
          unitPrice: 25.00,
          quantity: 5,
          paymentStatus: 'deuda',
          accountId: '1',
          accountName: 'Interbank Oro',
          orderNumber: 1,
          businessId
        }
      ]
      setOrders(sampleOrders)
      localStorage.setItem(`orders_${businessId}`, JSON.stringify(sampleOrders))
    }

    // Load accounts
    const savedAccounts = localStorage.getItem(`accounts_${businessId}`)
    if (savedAccounts) {
      setAccounts(JSON.parse(savedAccounts))
    } else {
      // Create sample accounts for new business
      const sampleAccounts: Account[] = [
        { id: '1', name: 'Interbank Oro', type: 'tarjeta', bank: 'Interbank', businessId },
        { id: '2', name: 'BCP Préstamo', type: 'prestamo', bank: 'BCP', businessId },
        { id: '3', name: 'Scotiabank Clásica', type: 'tarjeta', bank: 'Scotiabank', businessId }
      ]
      setAccounts(sampleAccounts)
      localStorage.setItem(`accounts_${businessId}`, JSON.stringify(sampleAccounts))
    }
  }, [currentBusiness])

  const addOrder = (orderData: Omit<Order, 'id' | 'orderNumber' | 'businessId'>) => {
    if (!currentBusiness) return

    const businessId = currentBusiness.id

    // Check if product exists, if not create it
    let product = products.find(p => p.name.toLowerCase() === orderData.productName.toLowerCase())
    if (!product) {
      product = {
        id: Date.now().toString(),
        name: orderData.productName,
        businessId
      }
      const updatedProducts = [...products, product]
      setProducts(updatedProducts)
      localStorage.setItem(`products_${businessId}`, JSON.stringify(updatedProducts))
    }

    // Calculate order number for the day
    const ordersToday = orders.filter(o => 
      o.productId === product!.id && o.purchaseDate === orderData.purchaseDate
    )
    const orderNumber = ordersToday.length + 1

    const newOrder: Order = {
      ...orderData,
      id: Date.now().toString(),
      productId: product.id,
      orderNumber,
      businessId,
      accountName: orderData.accountId ? accounts.find(a => a.id === orderData.accountId)?.name : undefined
    }

    const updatedOrders = [...orders, newOrder]
    setOrders(updatedOrders)
    localStorage.setItem(`orders_${businessId}`, JSON.stringify(updatedOrders))
  }

  const addAccount = (accountData: Omit<Account, 'id' | 'businessId'>) => {
    if (!currentBusiness) return

    const businessId = currentBusiness.id
    const newAccount: Account = {
      ...accountData,
      id: Date.now().toString(),
      businessId
    }

    const updatedAccounts = [...accounts, newAccount]
    setAccounts(updatedAccounts)
    localStorage.setItem(`accounts_${businessId}`, JSON.stringify(updatedAccounts))
  }

  const getDashboardData = () => {
    const totalProducts = products.length
    const totalStock = orders.reduce((sum, order) => sum + order.quantity, 0)
    
    // Calculate stock per product
    const productStock = products.map(product => {
      const stock = orders
        .filter(order => order.productId === product.id)
        .reduce((sum, order) => sum + order.quantity, 0)
      return { name: product.name, stock }
    })

    const lowStockProducts = productStock.filter(p => p.stock < 5)
    
    const recentPurchases = orders
      .sort((a, b) => new Date(b.purchaseDate).getTime() - new Date(a.purchaseDate).getTime())
      .slice(0, 5)
      .map(order => ({
        name: order.productName,
        quantity: order.quantity,
        price: order.unitPrice,
        date: order.purchaseDate
      }))

    return {
      totalProducts,
      totalStock,
      lowStockProducts,
      recentPurchases
    }
  }

  return (
    <DataContext.Provider value={{
      products,
      orders,
      accounts,
      addOrder,
      addAccount,
      getDashboardData
    }}>
      {children}
    </DataContext.Provider>
  )
}

export function useData() {
  const context = useContext(DataContext)
  if (context === undefined) {
    throw new Error('useData must be used within a DataProvider')
  }
  return context
}