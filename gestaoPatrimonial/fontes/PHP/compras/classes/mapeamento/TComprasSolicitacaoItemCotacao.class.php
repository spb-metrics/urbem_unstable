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
    * Classe de mapeamento da tabela compras.solicitacao_item_cotacao
    * Data de Criação: 30/06/2006

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Leandro André Zis

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 17467 $
    $Name$
    $Author: larocca $
    $Date: 2006-11-07 14:41:27 -0200 (Ter, 07 Nov 2006) $

    * Casos de uso: uc-03.04.01
*/

/*
$Log$
Revision 1.4  2006/11/07 16:41:27  larocca
Inclusão dos Casos de Uso

Revision 1.3  2006/07/06 14:05:54  diego
Retirada tag de log com erro.

Revision 1.2  2006/07/06 12:11:10  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  compras.solicitacao_item_cotacao
  * Data de Criação: 30/06/2006

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Leandro André Zis

  * @package URBEM
  * @subpackage Mapeamento
*/
class TComprasSolicitacaoItemCotacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TComprasSolicitacaoItemCotacao()
{
    parent::Persistente();
    $this->setTabela("compras.solicitacao_item_cotacao");

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio,cod_entidade,cod_solicitacao,cod_centro,cod_item');

    $this->AddCampo('exercicio','CHAR(4)',true,'',true,true);
    $this->AddCampo('cod_entidade','INTEGER',true,'',true,true);
    $this->AddCampo('cod_solicitacao','INTEGER',true,'',true,true);
    $this->AddCampo('cod_centro','INTEGER',true,'',true,true);
    $this->AddCampo('cod_item','INTEGER',true,'',true,true);
    $this->AddCampo('cgm_fornecedor','INTEGER',true,'',false,true);
    $this->AddCampo('exercicio_cotacao','CHAR(4)',true,'',false,true);
    $this->AddCampo('cod_cotacao','INTEGER',true,'',false,true);

}
}
