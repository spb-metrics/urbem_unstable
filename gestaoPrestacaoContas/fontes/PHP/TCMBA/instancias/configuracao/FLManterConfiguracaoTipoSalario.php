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
  * Página de Filtro de Configuração de Tipos de Salários
  * Data de Criação: 27/10/2015

  * @author Analista: Dagiane Vieira
  * @author Desenvolvedor: Arthur Cruz
  * @ignore
  *
  * $Id: $
  * $Revision: $
  * $Author: $
  * $Date: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_ORC_NEGOCIO.'ROrcamentoEntidade.class.php';

$stPrograma = 'ManterConfiguracaoTipoSalario';
$pgFilt = 'FL'.$stPrograma.'.php';
$pgList = 'LS'.$stPrograma.'.php';
$pgForm = 'FM'.$stPrograma.'.php';
$pgProc = 'PR'.$stPrograma.'.php';
$pgOcul = 'OC'.$stPrograma.'.php';
$pgJs   = 'JS'.$stPrograma.'.js';

$stAcao = $request->get('stAcao');

Sessao::remove('stEntidade');
Sessao::remove('arSchemasRH');
Sessao::remove('arFuncaoCargoServidor');
Sessao::remove('arFuncaoCargoServidorTemporario');
Sessao::remove('arBancoEventosEmprestimo');
Sessao::remove('arFonteRecursoLotacaoLocal');
Sessao::write("boEntidade",false);

$obForm = new Form;
$obForm->setAction($pgForm);
$obForm->setTarget('telaPrincipal');

$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue($stAcao);

$obHdnNomEntidade = new Hidden;
$obHdnNomEntidade->setId('stNomEntidade');
$obHdnNomEntidade->setName('stNomEntidade');

$obROrcamentoEntidade = new ROrcamentoEntidade;
$obROrcamentoEntidade->setExercicio(Sessao::getExercicio());
$obROrcamentoEntidade->listarEntidades($rsEntidades);

$obCmbEntidades = new Select  ();
$obCmbEntidades->setRotulo    ('Entidade');
$obCmbEntidades->setId        ('inCodEntidade');
$obCmbEntidades->setName      ('inCodEntidade');
$obCmbEntidades->setCampoId   ('cod_entidade');
$obCmbEntidades->setCampoDesc ('[nom_cgm]');
$obCmbEntidades->addOption    ('', 'Selecione');
$obCmbEntidades->setNull      ( false );
$obCmbEntidades->preencheCombo($rsEntidades);
$obCmbEntidades->obEvento->setOnBlur("jQuery('#stNomEntidade').val(jQuery('#inCodEntidade :selected').text());");

$obFormulario = new Formulario();
$obFormulario->addForm       ($obForm);
$obFormulario->addHidden     ($obHdnAcao);
$obFormulario->addHidden     ($obHdnNomEntidade);
$obFormulario->addComponente ($obCmbEntidades);
$obFormulario->Ok();

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>