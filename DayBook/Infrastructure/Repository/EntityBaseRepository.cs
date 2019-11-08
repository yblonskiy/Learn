using ApplicationCore.Interfaces;
using Infrastructure.Identity;
using Microsoft.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore.ChangeTracking;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace Infrastructure.Repository
{
    public class EntityBaseRepository<T> : IRepository<T> where T : class, new()
    {
        private readonly AppIdentityDbContext _context;

        public EntityBaseRepository(AppIdentityDbContext context)
        {
            _context = context;
        }

        public virtual async Task<IEnumerable<T>> GetAllAsync()
        {
            return await _context.Set<T>().ToListAsync();
        }

        public virtual async Task<IEnumerable<T>> AllIncludingAsync(params Expression<Func<T, object>>[] includeProperties)
        {
            IQueryable<T> query = _context.Set<T>();
            foreach (var includeProperty in includeProperties)
            {
                query = query.Include(includeProperty);
            }
            return await query.ToListAsync();
        }

        public virtual async Task<int> CountAsync()
        {
            return await _context.Set<T>().CountAsync();
        }

        public virtual async Task<T> GetSingleAsync(Expression<Func<T, bool>> predicate)
        {
            return await _context.Set<T>().FirstOrDefaultAsync(predicate);
        }

        public virtual async Task<T> GetSingleAsync(Expression<Func<T, bool>> predicate,
                                   params Expression<Func<T, object>>[] includeProperties)
        {
            IQueryable<T> query = _context.Set<T>();
            foreach (var includeProperty in includeProperties)
            {
                query = query.Include(includeProperty);
            }

            return await query.Where(predicate).FirstOrDefaultAsync();
        }

        public virtual IEnumerable<T> FindBy(Expression<Func<T, bool>> predicate)
        {
            return _context.Set<T>().Where(predicate);
        }

        public virtual async Task<bool> AddAsync(T entity)
        {
            EntityEntry dbEntityEntry = _context.Entry<T>(entity);
            await _context.Set<T>().AddAsync(entity);

            return await CommitAsync();
        }

        public virtual async Task<bool> UpdateAsync(T entity)
        {
            EntityEntry dbEntityEntry = _context.Entry<T>(entity);
            dbEntityEntry.State = EntityState.Modified;

            return await CommitAsync();
        }

        public virtual async Task<bool> DeleteAsync(T entity)
        {
            EntityEntry dbEntityEntry = _context.Entry<T>(entity);
            dbEntityEntry.State = EntityState.Deleted;

            return await CommitAsync();
        }

        public virtual async Task<bool> DeleteWhereAsync(Expression<Func<T, bool>> predicate)
        {
            IEnumerable<T> entities = _context.Set<T>().Where(predicate);

            foreach (var entity in entities)
            {
                _context.Entry<T>(entity).State = EntityState.Deleted;
            }

            return await CommitAsync();
        }

        public virtual async Task<bool> CommitAsync()
        {
            return await _context.SaveChangesAsync() > 0;
        }
    }
}
