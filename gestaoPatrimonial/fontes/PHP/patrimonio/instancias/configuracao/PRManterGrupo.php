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
 * Data de Criação: 05/09/2007

 * @author Analista: Gelson W. Gonçalves
 * @author Desenvolvedor: Henrique Boaventura

 * @package URBEM
 * @subpackage

 * Casos de uso: uc-03.01.04

 * $Id: PRManterGrupo.php 59612 2014-09-02 12:00:51Z gelson $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioGrupo.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioGrupoPlanoAnalitica.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioGrupoPlanoDepreciacao.class.php";

$stPrograma = "ManterGrupo";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

$obTPatrimonioGrupo = new TPatrimonioGrupo();
$obTPatrimonioGrupoPlanoAnalitica = new TPatrimonioGrupoPlanoAnalitica();
$obTPatrimonioGrupoPlanoDepreciacao = new TPatrimonioGrupoPlanoDepreciacao();

Sessao::setTrataExcecao( true );
Sessao::getTransacao()->setMapeamento( $obTPatrimonioGrupo );
Sessao::getTransacao()->setMapeamento( $obTPatrimonioGrupoPlanoAnalitica );

switch ($stAcao) {
    case 'incluir':

        $stFiltro = "
          WHERE  1=1
            AND  nom_grupo = '".$_REQUEST['stDescricaoGrupo']."'
            AND  grupo.cod_natureza = ".$_REQUEST['inCodNatureza']."
        ";
        $obTPatrimonioGrupo->recuperaGrupo( $rsPatrimonioGrupo, $stFiltro );
        
        if ($rsPatrimonioGrupo->getNumLinhas() <= 0) {
            $obTPatrimonioGrupo->setDado( 'cod_natureza', $_REQUEST['inCodNatureza'] );
            $obTPatrimonioGrupo->proximoCod( $inCodGrupo );
            $obTPatrimonioGrupo->setDado( 'cod_grupo'   , $inCodGrupo );
            $obTPatrimonioGrupo->setDado( 'nom_grupo'   , $_REQUEST['stDescricaoGrupo'] );
            
            $inDepreciacao = (!empty($_REQUEST['inDepreciacao'])&&$_REQUEST['inDepreciacao']>0) ? number_format(str_replace(',','.',$_REQUEST['inDepreciacao']),2,'.','') : '0.00';
            
            if(!empty($_REQUEST['inCodContaDepreciacao'])){
                if(empty($_REQUEST['inDepreciacao'])||$_REQUEST['inDepreciacao']==0){
                    SistemaLegado::exibeAviso(urlencode('Como o campo Conta Contábil de Depreciação Acumulada está preenchido, é obrigatório o preenchimento do campo Quota Depreciação Anual.'),"n_incluir","erro");
                    Sessao::encerraExcecao(); die;
                }
            }else{
                $inDepreciacao = '0.00';
            }
            
            $obTPatrimonioGrupo->setDado( 'depreciacao', $inDepreciacao );
            
            $obTPatrimonioGrupo->inclusao();
            
            if(!empty($_REQUEST['inCodConta'])){
                $obTPatrimonioGrupoPlanoAnalitica->setDado( 'cod_grupo'   , $inCodGrupo );
                $obTPatrimonioGrupoPlanoAnalitica->setDado( 'cod_natureza', $_REQUEST['inCodNatureza'] );
                $obTPatrimonioGrupoPlanoAnalitica->setDado( 'exercicio'   , Sessao::getExercicio() );
                $obTPatrimonioGrupoPlanoAnalitica->setDado( 'cod_plano'   , $_REQUEST['inCodConta'] );
                $obTPatrimonioGrupoPlanoAnalitica->inclusao();
            }
            if(!empty($_REQUEST['inCodContaDepreciacao'])){
                $obTPatrimonioGrupoPlanoDepreciacao->setDado( 'cod_grupo'   , $inCodGrupo );
                $obTPatrimonioGrupoPlanoDepreciacao->setDado( 'cod_natureza', $_REQUEST['inCodNatureza'] );
                $obTPatrimonioGrupoPlanoDepreciacao->setDado( 'exercicio'   , Sessao::getExercicio() );
                $obTPatrimonioGrupoPlanoDepreciacao->setDado( 'cod_plano'   , $_REQUEST['inCodContaDepreciacao'] );
                $obTPatrimonioGrupoPlanoDepreciacao->inclusao();
            }
            SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao,"Grupo - ".$inCodGrupo,"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode('Já existe um grupo com esta descrição para esta natureza'),"n_incluir","erro");
        }

    break;

    case 'alterar' :
        $stFiltro = "
          WHERE  1=1
            AND  nom_grupo = '".$_REQUEST['stDescricaoGrupo']."'
            AND  grupo.cod_natureza = ".$_REQUEST['inCodNatureza']."
            AND  grupo.cod_grupo <> ".$_REQUEST['inCodGrupo']."";

        $obTPatrimonioGrupo->recuperaGrupo($rsPatrimonioGrupo, $stFiltro);

        if ($rsPatrimonioGrupo->getNumLinhas() <= 0) {
            $obTPatrimonioGrupo->setDado( 'cod_natureza', $_REQUEST['inCodNatureza'] );
            $obTPatrimonioGrupo->proximoCod( $inCodGrupo );
            $obTPatrimonioGrupo->setDado( 'cod_grupo'   , $_REQUEST['inCodGrupo'] );
            $obTPatrimonioGrupo->setDado( 'nom_grupo'   , $_REQUEST['stDescricaoGrupo'] );
            
            $inDepreciacao = (!empty($_REQUEST['inDepreciacao'])) ? number_format(str_replace(',','.',$_REQUEST['inDepreciacao']),2,'.','') : '0.00';
            
            if(!empty($_REQUEST['inCodContaDepreciacao'])){
                if(empty($_REQUEST['inDepreciacao'])||$inDepreciacao==0.00){
                    SistemaLegado::exibeAviso(urlencode('Como o campo Conta Contábil de Depreciação Acumulada está preenchido, é obrigatório o preenchimento do campo Quota Depreciação Anual.'),"n_incluir","erro");
                    Sessao::encerraExcecao(); die;
                }
            }else{
                $inDepreciacao = '0.00';
            }
            
            $obTPatrimonioGrupo->setDado( 'depreciacao', $inDepreciacao );

            $obTPatrimonioGrupo->alteracao();

            //deleta da table grupo_plano_analitica
            $obTPatrimonioGrupoPlanoAnalitica->setDado( 'cod_natureza' , $_REQUEST['inCodNatureza'] );
            $obTPatrimonioGrupoPlanoAnalitica->setDado( 'cod_grupo'    , $_REQUEST['inCodGrupo'] );
            $obTPatrimonioGrupoPlanoAnalitica->setDado( 'exercicio'    , Sessao::getExercicio() );
            $obTPatrimonioGrupoPlanoAnalitica->exclusao();

            //inclui na table grupo_plano_analitica
            if(!empty($_REQUEST['inCodConta'])){
                $obTPatrimonioGrupoPlanoAnalitica->setDado( 'exercicio'    , Sessao::getExercicio() );
                $obTPatrimonioGrupoPlanoAnalitica->setDado( 'cod_natureza' , $_REQUEST['inCodNatureza'] );
                $obTPatrimonioGrupoPlanoAnalitica->setDado( 'cod_plano'    , $_REQUEST['inCodConta'] );
                $obTPatrimonioGrupoPlanoAnalitica->inclusao();
            }
            //deleta da table grupo_plano_depreciacao
            $obTPatrimonioGrupoPlanoDepreciacao->setDado( 'cod_natureza' , $_REQUEST['inCodNatureza'] );
            $obTPatrimonioGrupoPlanoDepreciacao->setDado( 'cod_grupo'    , $_REQUEST['inCodGrupo'] );
            $obTPatrimonioGrupoPlanoDepreciacao->setDado( 'exercicio'    , Sessao::getExercicio() );
            $obTPatrimonioGrupoPlanoDepreciacao->exclusao();

            //inclui na table grupo_plano_depreciacao
            if(!empty($_REQUEST['inCodContaDepreciacao'])){
                $obTPatrimonioGrupoPlanoDepreciacao->setDado( 'exercicio'    , Sessao::getExercicio() );
                $obTPatrimonioGrupoPlanoDepreciacao->setDado( 'cod_natureza' , $_REQUEST['inCodNatureza'] );
                $obTPatrimonioGrupoPlanoDepreciacao->setDado( 'cod_plano'    , $_REQUEST['inCodContaDepreciacao'] );
                $obTPatrimonioGrupoPlanoDepreciacao->inclusao();
             }
            SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,"Grupo - ".$_REQUEST['inCodGrupo'],"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode('Já existe um grupo com esta descrição para esta natureza'),"n_incluir","erro");
        }

    break;

    case 'excluir' :
        $obTPatrimonioGrupoPlanoAnalitica->setDado('cod_grupo'    , $_REQUEST['inCodGrupo'] );
        $obTPatrimonioGrupoPlanoAnalitica->setDado('cod_natureza' , $_REQUEST['inCodNatureza'] );
        $obTPatrimonioGrupoPlanoAnalitica->setDado('exercicio'    , Sessao::getExercicio() );
        $obTPatrimonioGrupoPlanoAnalitica->exclusao();
        
        //deleta da table grupo_plano_depreciacao
        $obTPatrimonioGrupoPlanoDepreciacao->setDado( 'cod_natureza' , $_REQUEST['inCodNatureza'] );
        $obTPatrimonioGrupoPlanoDepreciacao->setDado( 'cod_grupo'    , $_REQUEST['inCodGrupo'] );
        $obTPatrimonioGrupoPlanoDepreciacao->setDado( 'exercicio'    , Sessao::getExercicio() );
        $obTPatrimonioGrupoPlanoDepreciacao->exclusao();

        $obTPatrimonioGrupo->setDado('cod_natureza' , $_REQUEST['inCodNatureza'] );
        $obTPatrimonioGrupo->setDado('cod_grupo'    , $_REQUEST['inCodGrupo'] );
        $obTPatrimonioGrupo->exclusao();

        SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,"Grupo - ".$_REQUEST['inCodGrupo'],"excluir","aviso", Sessao::getId(), "../");

    break;

}

Sessao::encerraExcecao();
