using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using Ninject.Modules;
using YSD.Services.Interfaces;
using YSD.Services.Business;
using YSD.Infrastructure.Business;
using YSD.Infrastructure.Interfaces;

namespace YSD.Web.Util
{
    public class NinjectRegistrations : NinjectModule
    {
        public override void Load()
        {
            Bind<IUserService>().To<UserService>();
            Bind<IUnitOfWork>().To<EFUnitOfWork>().WithConstructorArgument("UserContext");
        }
    }
}