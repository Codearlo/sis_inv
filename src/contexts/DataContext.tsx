import React, { createContext, useContext, useState, useEffect } from 'react'

export interface Product {
  id: string
  name: string
  description?: string
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
}

export interface Account {
  id: string
  name: string
  type: 'tarjeta' | 'prestamo'
  bank?: string
}

interface DataContextType {
  products: Product[]
  orders: Order[]
  accounts: Account[]
  addOrder: (order: Omit<Order, 'id' | 'orderNumber'>) => void
  getDashboardData: () => {
    totalProducts: number
    totalStock: number
    lowStockProducts: Array<{ name: string; stock: number }>
    recentPurchases: Array<{ name: string; quantity: number; price: number; date: string }>
  }
}

const DataContext = createContext<DataContextType | undefined>(undefined)

export function DataProvider({ children }: { children: React.ReactNode }) {
  const [products, setProducts] = useState<Product[]>([])
  const [orders, setOrders] = useState<Order[]>([])
  const [accounts] = useState<Account[]>([
    { id: '1', name: 'Interbank Oro', type: 'tarjeta', bank: 'Interbank' },
    { id: '2', name: 'BCP Préstamo', type: 'prestamo', bank: 'BCP' },
    { id: '3', name: 'Scotiabank Clásica', type: 'tarjeta', bank: 'Scotiabank' }
  ])

  useEffect(() => {
    // Load sample data
    const sampleProducts: Product[] = [
      { id: '1', name: 'Teclado Mecánico RGB', description: 'Teclado gaming con switches azules' },
      { id: '2', name: 'Mouse Inalámbrico', description: 'Mouse ergonómico con sensor óptico' },
      { id: '3', name: 'Monitor 24"', description: 'Monitor Full HD IPS' }
    ]

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
        orderNumber: 1
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
        orderNumber: 1
      }
    ]

    setProducts(sampleProducts)
    setOrders(sampleOrders)
  }, [])

  const addOrder = (orderData: Omit<Order, 'id' | 'orderNumber'>) => {
    // Check if product exists, if not create it
    let product = products.find(p => p.name.toLowerCase() === orderData.productName.toLowerCase())
    if (!product) {
      product = {
        id: Date.now().toString(),
        name: orderData.productName
      }
      setProducts(prev => [...prev, product!])
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
      accountName: orderData.accountId ? accounts.find(a => a.id === orderData.accountId)?.name : undefined
    }

    setOrders(prev => [...prev, newOrder])
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