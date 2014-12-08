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
    * Página de Formulario de Seleção de Impressora para Relatorio
    * Data de Criação   : 26/01/2006

    * @author Analista: Lucas Leusin Oiagen
    * @author Desenvolvedor: Jose Eduardo Porto

    * @ignore

    $Revision: 30835 $
    $Name$
    $Autor:$
    $Date: 2007-03-30 19:00:14 -0300 (Sex, 30 Mar 2007) $

    * Casos de uso: uc-02.04.20
*/

/*
$Log$
Revision 1.5  2007/03/30 21:58:02  cako
Bug #7884#

Revision 1.4  2006/07/05 20:39:28  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                             );

$arFiltro = Sessao::read('filtro');
$arFiltroRelatorio = Sessao::read('filtroRelatorio');

if ($arFiltroRelatorio['stTipoBordero'] == "T") {

    include_once( CAM_GF_TES_NEGOCIO."RTesourariaRelatorioBorderoTransferencia.class.php"  );

    $obRRelatorio                               = new RRelatorio;
    $obRTesourariaRelatorioBorderoTransferencia = new RTesourariaRelatorioBorderoTransferencia;

    $obRTesourariaRelatorioBorderoTransferencia->setCodBordero($arFiltroRelatorio['inCodBordero']);
    $obRTesourariaRelatorioBorderoTransferencia->setCodEntidade($arFiltroRelatorio['inCodEntidade']);
    $obRTesourariaRelatorioBorderoTransferencia->setExercicio($arFiltroRelatorio['stExercicio']);

    $obRTesourariaRelatorioBorderoTransferencia->geraRecordSetBorderoTransferencia( $rsBorderoTransferencia );

    Sessao::write('filtro',$rsBorderoTransferencia);

    $obRRelatorio->executaFrameOculto( "OCGeraRelatorioBorderoTransferencia.php" );

} elseif ($arFiltroRelatorio['stTipoBordero'] == "P") {

    include_once( CAM_GF_TES_NEGOCIO."RTesourariaRelatorioBorderoPagamento.class.php"  );

    $obRRelatorio                           = new RRelatorio;
    $obRTesourariaRelatorioBorderoPagamento = new RTesourariaRelatorioBorderoPagamento;

    $obRTesourariaRelatorioBorderoPagamento->setCodBordero($arFiltroRelatorio['inCodBordero']);
    $obRTesourariaRelatorioBorderoPagamento->setCodEntidade($arFiltroRelatorio['inCodEntidade']);
    $obRTesourariaRelatorioBorderoPagamento->setExercicio($arFiltroRelatorio['stExercicio']);
    $obRTesourariaRelatorioBorderoPagamento->setCodOrdem($arFiltroRelatorio['stCodOrdem']);

    $obRTesourariaRelatorioBorderoPagamento->geraRecordSetBorderoPagamento( $rsBorderoPagamento );

    Sessao::write('filtro', $rsBorderoPagamento);

    $obRRelatorio->executaFrameOculto( "../../../../../../gestaoFinanceira/fontes/PHP/tesouraria/instancias/pagamentos/OCGeraRelatorioBorderoPagamento.php" );
}
?>
