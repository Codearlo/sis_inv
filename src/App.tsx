import React from 'react'
import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom'
import { AuthProvider, useAuth } from './contexts/AuthContext'
import { BusinessProvider } from './contexts/BusinessContext'
import { DataProvider } from './contexts/DataContext'
import Login from './components/Login'
import Dashboard from './components/Dashboard'
import Pedidos from './components/Pedidos'
import NuevoPedido from './components/NuevoPedido'
import BusinessManager from './components/BusinessManager'
import Layout from './components/Layout'

function ProtectedRoute({ children }: { children: React.ReactNode }) {
  const { isAuthenticated } = useAuth()
  return isAuthenticated ? <>{children}</> : <Navigate to="/login" />
}

function App() {
  return (
    <AuthProvider>
      <BusinessProvider>
        <DataProvider>
          <Router>
            <Routes>
              <Route path="/login" element={<Login />} />
              <Route path="/" element={
                <ProtectedRoute>
                  <Layout>
                    <Dashboard />
                  </Layout>
                </ProtectedRoute>
              } />
              <Route path="/dashboard" element={
                <ProtectedRoute>
                  <Layout>
                    <Dashboard />
                  </Layout>
                </ProtectedRoute>
              } />
              <Route path="/pedidos" element={
                <ProtectedRoute>
                  <Layout>
                    <Pedidos />
                  </Layout>
                </ProtectedRoute>
              } />
              <Route path="/nuevo-pedido" element={
                <ProtectedRoute>
                  <Layout>
                    <NuevoPedido />
                  </Layout>
                </ProtectedRoute>
              } />
              <Route path="/negocios" element={
                <ProtectedRoute>
                  <Layout>
                    <BusinessManager />
                  </Layout>
                </ProtectedRoute>
              } />
            </Routes>
          </Router>
        </DataProvider>
      </BusinessProvider>
    </AuthProvider>
  )
}

export default App