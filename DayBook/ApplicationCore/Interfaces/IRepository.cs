using System;
using System.Collections.Generic;
using System.Linq.Expressions;
using System.Threading.Tasks;

namespace ApplicationCore.Interfaces
{
    public interface IRepository<T> where T : class
    {
        Task<IEnumerable<T>> GetAllAsync();

        Task<IEnumerable<T>> AllIncludingAsync(params Expression<Func<T, object>>[] includeProperties);

        Task<int> CountAsync();

        Task<T> GetSingleAsync(Expression<Func<T, bool>> predicate);

        Task<T> GetSingleAsync(Expression<Func<T, bool>> predicate, params Expression<Func<T, object>>[] includeProperties);

        IEnumerable<T> FindBy(Expression<Func<T, bool>> predicate);

        Task<bool> AddAsync(T entity);

        Task<bool> UpdateAsync(T entity);

        Task<bool> DeleteAsync(T entity);

        Task<bool> DeleteWhereAsync(Expression<Func<T, bool>> predicate);

        Task<bool> CommitAsync();
    }
}
