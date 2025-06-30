import React from 'react'
import { Link, useLocation } from 'react-router-dom'
import { useAuth } from '../contexts/AuthContext'
import { Package, LayoutDashboard, ShoppingCart, LogOut, Settings } from 'lucide-react'

export default function Sidebar() {
  const location = useLocation()
  const { logout } = useAuth()

  const navigation = [
    { name: 'Dashboard', href: '/dashboard', icon: LayoutDashboard },
    { name: 'Pedidos', href: '/pedidos', icon: ShoppingCart },
    { name: 'Productos', href: '#', icon: Settings },
  ]

  const isActive = (href: string) => {
    if (href === '/dashboard' && (location.pathname === '/' || location.pathname === '/dashboard')) {
      return true
    }
    return location.pathname === href
  }

  return (
    <div className="fixed left-2 top-2 bottom-2 w-16 bg-gray-900 rounded-2xl flex flex-col z-50">
      {/* Header */}
      <div className="flex items-center justify-center h-16 flex-shrink-0">
        <Package className="h-7 w-7 text-white" />
      </div>

      {/* Navigation */}
      <nav className="flex-1 flex flex-col items-center py-4 space-y-2">
        {navigation.map((item) => {
          const Icon = item.icon
          return (
            <Link
              key={item.name}
              to={item.href}
              className={`
                group relative flex items-center justify-center w-10 h-10 rounded-xl transition-all duration-200
                ${isActive(item.href)
                  ? 'bg-white text-gray-900'
                  : 'text-gray-400 hover:text-white hover:bg-gray-800'
                }
              `}
            >
              <Icon className="h-5 w-5" />
              
              {/* Tooltip */}
              <div className="absolute left-14 px-3 py-1 bg-gray-800 text-white text-sm rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 whitespace-nowrap z-50">
                {item.name}
                <div className="absolute left-0 top-1/2 transform -translate-y-1/2 -translate-x-1 w-2 h-2 bg-gray-800 rotate-45"></div>
              </div>
            </Link>
          )
        })}
      </nav>

      {/* Logout */}
      <div className="flex-shrink-0 p-4">
        <button
          onClick={logout}
          className="group relative flex items-center justify-center w-10 h-10 rounded-xl text-gray-400 hover:text-red-400 hover:bg-red-50 transition-all duration-200"
        >
          <LogOut className="h-5 w-5" />
          
          {/* Tooltip */}
          <div className="absolute left-14 px-3 py-1 bg-gray-800 text-white text-sm rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 whitespace-nowrap z-50">
            Cerrar Sesión
            <div className="absolute left-0 top-1/2 transform -translate-y-1/2 -translate-x-1 w-2 h-2 bg-gray-800 rotate-45"></div>
          </div>
        </button>
      </div>
    </div>
  )
}