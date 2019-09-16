using Ninject.Modules;
using CustomerKeys.DAL.Interfaces;
using CustomerKeys.DAL.Repositories;

namespace CustomerKeys.BLL.Infrastructure
{
    public class ServiceModule : NinjectModule
    {
        private string connectionString;

        public ServiceModule(string connection)
        {
            connectionString = connection;
        }

        public override void Load()
        {
            Bind<IUnitOfWork>().To<EFUnitOfWork>().WithConstructorArgument(connectionString);
        }
    }
}
