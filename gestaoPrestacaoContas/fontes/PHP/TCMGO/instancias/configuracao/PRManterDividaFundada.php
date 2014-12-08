<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
    * Página de Processamento
    * Data de Criação   : 16/04/2007

    * @author Henrique Boaventura

    * @ignore

    $Id: PRManterDividaFundada.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso : uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(TTGO.'TTGOGrupoPlanoAnalitica.class.php');
include_once(TTGO.'TTGOGrupoPlanoAnaliticaLei.class.php');

//Define o nome dos arquivos PHP
$stPrograma = "ManterDividaFundada";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

Sessao::setTrataExcecao ( true );
$stAcao = $request->get('stAcao');
$arContas = Sessao::read('arContas');

switch ($_REQUEST['stAcao']) {
    case 'incluir' :

        if ($arContas > 0) {

            $obTTGOGrupoPlanoAnalitica = new TTGOGrupoPlanoAnalitica();
            $obTTGOGrupoPlanoAnaliticaLei = new TTGOGrupoPlanoAnaliticaLei();

            $obTTGOGrupoPlanoAnalitica->setDado('exercicio',Sessao::getExercicio());
            $obTTGOGrupoPlanoAnalitica->setDado('cod_tipo_lancamento','2');
            $obTTGOGrupoPlanoAnalitica->setDado('cod_tipo',$_REQUEST['inTipoLancamento']);
            $obTTGOGrupoPlanoAnalitica->recuperaGrupoPlanoAnalitica( $rsContas );

            while ( !$rsContas->eof() ) {
                $obTTGOGrupoPlanoAnaliticaLei->setDado( 'exercicio', Sessao::getExercicio() );
                $obTTGOGrupoPlanoAnaliticaLei->setDado( 'cod_plano', $rsContas->getCampo('cod_plano') );
                $obTTGOGrupoPlanoAnaliticaLei->exclusao();
                $obTTGOGrupoPlanoAnalitica->setDado('cod_plano',$rsContas->getCampo('cod_plano'));
                $obTTGOGrupoPlanoAnalitica->exclusao();
                $rsContas->proximo();
            }

            foreach ($arContas as $arAux) {
                $obTTGOGrupoPlanoAnalitica->setDado('cod_plano',$arAux['cod_plano']);
                $obTTGOGrupoPlanoAnalitica->inclusao();
                $obTTGOGrupoPlanoAnaliticaLei->setDado( 'cod_plano', $arAux['cod_plano'] );
                $obTTGOGrupoPlanoAnaliticaLei->setDado( 'exercicio', Sessao::getExercicio() );
                $obTTGOGrupoPlanoAnaliticaLei->setDado( 'nro_lei', $arAux['lei_autorizacao'] );
                $obTTGOGrupoPlanoAnaliticaLei->setDado( 'data_lei', $arAux['data_autorizacao'] );
                $obTTGOGrupoPlanoAnaliticaLei->inclusao();
            }

            SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=$stAcao","Configuração ","incluir","incluir_n", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode('É necessário cadastrar pelo uma conta!'),"n_incluir","erro");
        }
}

Sessao::encerraExcecao();
