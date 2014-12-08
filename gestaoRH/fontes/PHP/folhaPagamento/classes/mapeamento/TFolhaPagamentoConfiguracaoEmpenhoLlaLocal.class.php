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
    * Classe de mapeamento da tabela folhapagamento.configuracao_empenho_lla_local
    * Data de Criação: 10/07/2007

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-07-17 10:02:38 -0300 (Ter, 17 Jul 2007) $

    * Casos de uso: uc-04.05.29
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  folhapagamento.configuracao_empenho_lla_local
  * Data de Criação: 10/07/2007

  * @author Analista: Dagiane Vieira
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoConfiguracaoEmpenhoLlaLocal extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoConfiguracaoEmpenhoLlaLocal()
{
    parent::Persistente();
    $this->setTabela("folhapagamento.configuracao_empenho_lla_local");

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio,cod_local,cod_configuracao_lla,timestamp');

    $this->AddCampo('exercicio'           ,'char'     ,true  ,'4'  ,true,'TFolhaPagamentoConfiguracaoEmpenhoLla');
    $this->AddCampo('cod_local'           ,'integer'  ,true  ,''   ,true,'TOrganogramaLocal');
    $this->AddCampo('cod_configuracao_lla','integer'  ,true  ,''   ,true,'TFolhaPagamentoConfiguracaoEmpenhoLla');
    $this->AddCampo('timestamp'           ,'timestamp',true  ,''   ,true,'TFolhaPagamentoConfiguracaoEmpenhoLla');
    $this->AddCampo('num_pao'             ,'integer'  ,true  ,''   ,false,'TOrcamentoProjetoAtividade');

}

function montaRecuperaRelacionamento()
{
    $stSql  = "SELECT configuracao_empenho_lla_local.*                                      \n";
    $stSql .= "     , local.descricao                                                       \n";
    $stSql .= "  FROM folhapagamento.configuracao_empenho_lla_local                         \n";
    $stSql .= "     , organograma.local                                                     \n";
    $stSql .= " WHERE configuracao_empenho_lla_local.cod_local = local.cod_local            \n";

    return $stSql;
}
}
?>
