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
    * Classe de mapeamento da tabela ALMOXARIFADO.ATRIBUTO_CATALOGO_CLASSIFICACAO_ITEM_VALOR
    * Data de Criação: 26/10/2005

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 12234 $
    $Name$
    $Author: diego $
    $Date: 2006-07-06 11:08:37 -0300 (Qui, 06 Jul 2006) $

    * Casos de uso: uc-03.03.06
*/

/*
$Log$
Revision 1.9  2006/07/06 14:04:43  diego
Retirada tag de log com erro.

Revision 1.8  2006/07/06 12:09:27  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ALMOXARIFADO.ATRIBUTO_CATALOGO_CLASSIFICACAO_ITEM_VALOR
  * Data de Criação: 26/10/2005

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Fernando Zank Correa Evangelista

  * @package URBEM
  * @subpackage Mapeamento
*/
class TAlmoxarifadoAtributoCatalogoClassificacaoItemValor extends PersistenteAtributosValores
{
/**
    * Método Construtor
    * @access Private
*/
function TAlmoxarifadoAtributoCatalogoClassificacaoItemValor()
{
    parent::PersistenteAtributosValores();
    $this->setTabela('almoxarifado.atributo_catalogo_classificacao_item_valor');
    $this->setPersistenteAtributo ( new TAlmoxarifadoAtributoCatalogoClassificacao );

    $this->setCampoCod('');
    $this->setComplementoChave('cod_classificacao,cod_modulo,cod_cadastro,cod_atributo,cod_item,cod_catalogo');

    $this->AddCampo('cod_classificacao','integer',true,'',true,true);
    $this->AddCampo('cod_modulo','integer',true,'',true,true);
    $this->AddCampo('cod_cadastro','integer',true,'',true,true);
    $this->AddCampo('cod_atributo','integer',true,'',true,true);
    $this->AddCampo('cod_item','integer',true,'',true,false);
    $this->AddCampo('cod_catalogo','integer',true,'',true,true);
    $this->AddCampo('timestamp','timestamp',false,'',false,false);
    $this->AddCampo('valor','varchar',true,'1500',false,false);

}
}
