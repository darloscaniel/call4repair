// Lightweight client-side auth/permission helpers backed by sessionStorage.
// Authorization is always enforced server-side; this only drives UI visibility.

export function setAuth({ token, roles, permissions }) {
  sessionStorage.setItem('token', token)
  sessionStorage.setItem('roles', JSON.stringify(roles || []))
  sessionStorage.setItem('permissions', JSON.stringify(permissions || []))
}

export function clearAuth() {
  sessionStorage.removeItem('token')
  sessionStorage.removeItem('roles')
  sessionStorage.removeItem('permissions')
}

export function getPermissions() {
  try {
    return JSON.parse(sessionStorage.getItem('permissions') || '[]')
  } catch {
    return []
  }
}

export function can(permission) {
  return getPermissions().includes(permission)
}

export function isAuthenticated() {
  return !!sessionStorage.getItem('token')
}
