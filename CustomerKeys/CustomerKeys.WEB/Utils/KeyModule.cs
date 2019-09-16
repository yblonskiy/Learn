using Ninject.Modules;
using CustomerKeys.BLL.Interfaces;
using CustomerKeys.BLL.Services;

namespace CustomerKeys.WEB.Utils
{
    public class KeyModule : NinjectModule
    {
        public override void Load()
        {
            Bind<IKeyService>().To<KeyService>();
        }
    }
}