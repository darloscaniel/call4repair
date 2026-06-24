// Lightweight client-side auth/permission helpers backed by sessionStorage.
// The JWT lives in an httpOnly cookie (not readable by JS); sessionStorage only
// holds non-sensitive UI state (roles/permissions) and an "authenticated" flag.
// Authorization is always enforced server-side; this only drives UI visibility.

export function setAuth({ roles, permissions }) {
  sessionStorage.setItem('authenticated', '1')
  sessionStorage.setItem('roles', JSON.stringify(roles || []))
  sessionStorage.setItem('permissions', JSON.stringify(permissions || []))
}

export function clearAuth() {
  sessionStorage.removeItem('authenticated')
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
  return sessionStorage.getItem('authenticated') === '1'
}
